<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Controller;

use Phosphorum\Model\Users;
use Phosphorum\Model\Posts;
use Phosphorum\Model\PostsReplies;
use Phosphorum\Model\Karma;
use Phosphorum\Model\PostsBounties;
use Phosphorum\Model\NotificationsBounces;
use Phalcon\Http\Response;
use Aws\Sns\MessageValidator\Message;
use Aws\Sns\MessageValidator\MessageValidator;

class HooksController extends ControllerBase
{
    const RE_DATE_TIME_1 = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2} GMT([\-\+][0-9]{2}:[0-9]{2})? ([^:]*):$/u';
    const RE_DATE_TIME_2 = '/^On [A-Za-z]{3} [0-9]{1,2}, [0-9]{4} [0-9]{1,2}:[0-9]{2} [AP]M, ([^:]*):$/u';

    /**
     * This implements an inbound webhook from MandrillApp to reply to posts using emails
     */
    public function mailReplyAction()
    {
        $response = new Response();
        if ($this->request->isPost()) {
            if (!isset($this->config->mandrillapp->secret)) {
                return $response;
            }

            if ($this->config->mandrillapp->secret != $this->request->getQuery('secret')) {
                return $response;
            }

            $events = @json_decode($this->request->getPost('mandrill_events'), true);
            if (!is_array($events)) {
                return $response;
            }

            foreach ($events as $event) {
                if (!isset($event['event'])) {
                    continue;
                }

                $type = $event['event'];
                if ($type != 'inbound') {
                    continue;
                }

                if (!isset($event['msg'])) {
                    continue;
                }

                $msg = $event['msg'];
                if (!isset($msg['dkim'])) {
                    continue;
                }

                if (!isset($msg['from_email'])) {
                    continue;
                }

                if (!isset($msg['email'])) {
                    continue;
                }

                if (!isset($msg['text'])) {
                    continue;
                }

                $content = $msg['text'];
                if (!trim($content)) {
                    continue;
                }

                $user = Users::findFirstByEmail($msg['from_email']);
                if (!$user) {
                    continue;
                }

                $email = $msg['email'];
                if (!preg_match('#^reply-i([0-9]+)-([0-9]+)@phosphorum.com$#', $email, $matches)) {
                    continue;
                }

                $post = Posts::findFirst($matches[1]);
                if (!$post) {
                    continue;
                }

                if ($post->deleted) {
                    continue;
                }

                /**
                 * Process replies to remove the base message
                 */
                $str = [];
                $firstNoBaseReplyLine = false;
                foreach (array_reverse(preg_split('/\r\n|\n/', trim($content))) as $line) {
                    if (!$firstNoBaseReplyLine) {
                        if (substr($line, 0, 1) == '>') {
                            continue;
                        } else {
                            $firstNoBaseReplyLine = true;
                        }
                    }

                    if (preg_match(self::RE_DATE_TIME_1, $line)) {
                        continue;
                    }

                    if (preg_match(self::RE_DATE_TIME_2, $line)) {
                        continue;
                    }

                    $str[] = $line;
                }

                $content = join("\r\n", array_reverse($str));

                /**
                 * Check if the question can have a bounty before add the reply
                 */
                $canHaveBounty = $post->canHaveBounty();

                /**
                 * Only update the number of replies if the user that commented isn't the same that posted
                 */
                if ($post->users_id != $user->id) {
                    $post->number_replies++;
                    $post->modified_at = time();
                    $post->user->increaseKarma(Karma::SOMEONE_REPLIED_TO_MY_POST);

                    $user->increaseKarma(Karma::REPLY_ON_SOMEONE_ELSE_POST);
                    $user->save();
                }

                $postReply = new PostsReplies();
                $postReply->post = $post;
                $postReply->users_id = $user->id;
                $postReply->content = $content;

                if ($postReply->save()) {
                    if ($post->users_id != $user->id && $canHaveBounty) {
                        $bounty = $post->getBounty();
                        $postBounty = new PostsBounties();
                        $postBounty->posts_id = $post->id;
                        $postBounty->users_id = $user->id;
                        $postBounty->posts_replies_id = $postReply->id;
                        $postBounty->points = $bounty['value'];
                        if (!$postBounty->save()) {
                            foreach ($postBounty->getMessages() as $message) {
                                $this->flash->error($message);
                            }
                        }
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Amazon SNS endpoint to receive bounces and complains notified by Amazon SES
     *
     */
    public function mailBounceAction()
    {

        $response = new Response();

        $body = $this->request->getRawBody();

        $data = @json_decode($body, true);
        if (!is_array($data)) {
            return $response;
        }

        $message = Message::fromArray($data);

        $validator = new MessageValidator();
        if ($validator->isValid($message)) {
            $notification = json_decode($message->get('Message'), true);
            if (is_array($notification)) {
                do {
                    if (!isset($notification['notificationType'])) {
                        break;
                    }

                    if ($notification['notificationType'] == 'Bounce') {
                        if (!isset($notification['bounce'])) {
                            break;
                        }

                        $bounce = $notification['bounce'];
                        if (!isset($bounce['bouncedRecipients']) || !is_array($bounce['bouncedRecipients'])) {
                            break;
                        }

                        foreach ($bounce['bouncedRecipients'] as $recipient) {
                            $notificationBounce = new NotificationsBounces();
                            $notificationBounce->email = $recipient['emailAddress'];
                            $notificationBounce->status = $recipient['status'];
                            $notificationBounce->diagnostic = $recipient['diagnosticCode'];
                            $notificationBounce->save();
                        }
                    }
                } while (0);
            }
        }

        return $response;
    }
}
