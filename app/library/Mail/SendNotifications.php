<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Mail;

use Phalcon\Di;
use Aws\Sqs\SqsClient;
use Phalcon\Di\Injectable;
use Aws\AwsClientInterface;
use Phalcon\Ext\Mailer\Manager;
use Phalcon\Ext\Mailer\Message;
use Aws\Exception\AwsException;
use Phosphorum\Model\Notifications;
use Phosphorum\Model\Services\Service;

/**
 * Phosphorum\Mail\SendNotifications
 *
 * @property AwsClientInterface $queue
 * @package Phosphorum\Mail
 */
class SendNotifications extends Injectable
{
    /** @var Notifications $notification */
    protected $notification;

    /** @var Service\Notifications $notificationService */
    protected $notificationService;

    /** @var Service\Users $userService */
    protected $userService;

    /** @var Manager $mailer */
    public $mailer;

    /** @var \Phalcon\Config */
    protected $config;

    /** @var string $content */
    protected $content;

    public function __construct()
    {
        $this->notificationService = container(Service\Notifications::class);
        $this->userService = container(Service\Users::class);
        $this->config = container('config');
        $this->mailer = container('mailer');
    }

    /**
     * Check the queue from Amazon SQS and send the notifications scheduled there
     * @return void
     */
    public function consumeQueue()
    {
        try {
            $queue = Di::getDefault()->get('queue');

            /** @var \Aws\Result $messages*/
            $messages = $this->getMessagesFromQueue($queue);

            if (count($messages->get('Messages')) == 0) {
                return;
            }
            foreach ($messages->get('Messages') as $message) {
                $idList = json_decode($message['Body'], true);

                if (!empty($idList)) {
                    $this->trySendNotificationsFromMessage($idList);
                }

                $queue->deleteMessage([
                    'QueueUrl' => $queue->getQueueUrl(['QueueName' => 'notifications'])->get('QueueUrl'),
                    'ReceiptHandle' => $message['ReceiptHandle'],
                ]);
            }
        } catch (AwsException $e) {
            Di::getDefault()->get('logger')->error($e->getMessage());
        } catch (\Exception $e) {
            // Do nothing
        } catch (\Throwable $e) {
            // Do nothing
        }

        sleep(5);
        $this->consumeQueue();
    }

    /**
     * Get messages from queue, max amount 10
     * @param SqsClient $queue
     * @return \Aws\Result
     */
    protected function getMessagesFromQueue(SqsClient $queue)
    {
        return $queue->receiveMessage([
            'AttributeNames' => ['SentTimestamp'],
            'MaxNumberOfMessages' => 10,
            'MessageAttributeNames' => ['All'],
            'QueueUrl' => $queue->getQueueUrl(['QueueName' => 'notifications'])->get('QueueUrl'),
        ]);
    }

    /**
     * Try to send to users from queue's message
     * @param array $idList
     * @return void
     * @throws \Exception|\Throwable
     */
    protected function trySendNotificationsFromMessage(array $idList)
    {
        foreach ($idList as $userId => $id) {
            if (!$notification = Notifications::findFirstById($id)) {
                return;
            }

            try {
                $this->send($notification);
            } catch (\Exception $e) {
                // Do nothing
            } catch (\Throwable $t) {
                // Do nothing
            }
        }
    }

    /**
     * Check notifications marked as not send on the databases and send them
     * @return void
     */
    public function sendRemaining()
    {
        $notifications = Notifications::find([
            'conditions' => 'sent = ?1 AND UNIX_TIMESTAMP() - created_at <= ?2',
            'order'      => 'created_at DESC',
            'limit'      => 1000,
            'bind'       => [
                1 => Notifications::STATUS_NOT_SENT, // Only unsent messages
                2 => 7 * 24 * 60 * 60,               // For last 7 days
            ],
        ]);

        foreach ($notifications as $notification) {
            $this->send($notification);
        }
    }

    /**
     * @param Notifications $notification
     * @return void
     */
    protected function send(Notifications $notification)
    {
        $this->setNotificationParam($notification);
        if (!$this->checkMailCanBeSent()) {
            return;
        }

        $message = $this->getLetter();
        $message->send();
        $this->notificationService->markAsCompleted($notification);
    }

    /**
     * Set params for notification
     * @param Notifications $notification
     * @return void
     */
    protected function setNotificationParam(Notifications $notification)
    {
        $this->notification = $notification;
        $this->content = $this->notificationService->getContentsForNotification($notification);
    }

    /**
     * Checking different params that allow sending email
     * @return bool
     */
    protected function checkMailCanBeSent(): bool
    {
        if (!$this->checkNotificationCanBeSent()) {
            return false;
        }

        if (!$this->checkEmailIsValid()) {
            return false;
        }

        if (!$this->checkUserExpectsNotification()) {
            return false;
        }

        if (!$this->checkMailContent()) {
            return false;
        }

        return true;
    }

    /**
     * Set option to object Message for sending email
     * @return Message
     */
    protected function getLetter(): Message
    {
        $mailParams = $this->prepareAndGetMailParams();

        $message = $this->mailer->createMessage()
            ->to($this->notification->user->email, $this->notification->user->name)
            ->subject($mailParams['subject']);

        $message->content($mailParams['html_content'], $message::CONTENT_TYPE_HTML);
        $message->contentAlternative($mailParams['text_content'], $message::CONTENT_TYPE_PLAIN);
        $message->replyTo("reply-i{$this->notification->post->id}-" . time() . '@phosphorum.com');
        $message->from($this->notificationService->getFromUser($this->notification));

        $this->prepareMessageId($message);

        return $message;
    }

    /**
     * @return bool
     */
    protected function checkNotificationCanBeSent(): bool
    {
        if (!$this->notificationService->isReadyToBeSent($this->notification)) {
            return false ;
        }

        if (!$this->notificationService->hasRequiredIntegrity($this->notification)) {
            $this->notificationService->markAsInvalid($this->notification);
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function checkEmailIsValid(): bool
    {
        /** @var \Phosphorum\Email\EmailComponent $email */
        $email = container('email', [$this->notification->user->email, false]);
        if (!$email->valid()) {
            $this->notificationService->markAsSkipped($this->notification);
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function checkUserExpectsNotification(): bool
    {
        if (!$this->userService->doesExpectNotifications($this->notification->user)) {
            $this->notificationService->markAsSkipped($this->notification);
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function checkMailContent(): bool
    {
        if (!trim($this->content)) {
            $this->notificationService->markAsInvalid($this->notification);
            return false;
        }

        return true;
    }

    /**
     * prepare and return param for email
     * @return $array
     */
    protected function prepareAndGetMailParams(): array
    {
        $sendParam['title'] = strip_tags($this->notification->post->title);
        $sendParam['subject'] = "[{$this->config->site->name}] " . strip_tags($this->notification->post->title);
        $sendParam['post_url'] = $this->notificationService->getRelatedPostUrl($this->notification);
        $sendParam['settings_url'] = container('config')->site->url . '/settings';
        $sendParam['html_content'] = $this->prepareHtmlContent($sendParam);
        $sendParam['text_content'] = $this->getTextContent($this->prepareTextlContent($sendParam));

        return $sendParam;
    }

    /**
     * Sets mail Message-ID
     *
     * Return example:
     * <code>
     * 14564793977738.123.3345.2344@forum.phalconphp.com
     * </code>
     *
     * @param  Message       $message
     * @return string
     */
    protected function prepareMessageId(Message $message): string
    {
        $msgId  = $message->getSwiftMessage()->getHeaders()->get('Message-ID');
        $parsed = parse_url(container('config')->site->url);

        $id = sprintf(
            '%d.%d.%d.%d@%s',
            str_replace('.', '', microtime(true)),
            $this->notification->id,
            $this->notification->posts_id,
            $this->notification->users_id,
            isset($parsed['host']) ? $parsed['host'] : 'phosphorum.com'
        );

        $msgId->setId($id);

        return $id;
    }

    /**
     * @return string
     */
    protected function prepareHtmlContent(array $param): string
    {
        $param['html_content'] = container('markdown')->render($this->content);

        return $this->prepareContent('mail/notification', $param);
    }

    /**
     * Prepares and returns plain text from content
     *
     * @param string $message
     * @return string
     */
    protected function getTextContent(string $message): string
    {
        $message = preg_replace('#<a[^>]+href="([^"]+)"[^>]*>([^<]+)<\/a>#', '$2:' . "\n" . '$1', $message);
        $message = trim($this->stripHtmlTags($message));
        $message = str_replace('&nbsp;', ' ', $message);
        $message = preg_replace('#\t+#', '', $message);
        $message = preg_replace('# {2,}#', ' ', $message);
        $message = preg_replace('#(\r\n|\r|\n){2,}#m', "\n\n", $message);

        $message = explode("\n\n", $message);

        $text  = [];
        foreach ($message as $n => $line) {
            $line = trim($line);
            if (!empty($line)) {
                $text[] = $line;
            }
        }

        $message = implode("\n\n", $text);
        $message = preg_replace('#^[ \t]+#m', '', $message);

        return str_replace('&mdash;', '--', $message);
    }

    /**
     * @return string
     */
    protected function prepareTextlContent(array $param): string
    {
        $param['html_content'] = $this->content;

        return $this->prepareContent('mail/notification', $param);
    }

    /**
     * Prepare mail content, using template file.
     *
     * @param string     $viewPath
     * @param array|null $params
     *
     * @return string
     */
    protected function prepareContent(string $viewPath, array $params = null)
    {
        $view = container('view');

        ob_start();

        $view->render($viewPath, $params);

        ob_end_clean();

        return (string) $view->getContent();
    }

    /**
     * Remove HTML tags, including invisible text such as style and
     * script code, and embedded objects.  Add line breaks around
     * block-level tags to prevent word joining after tag removal.
     *
     * @param  string $text
     * @return string
     */
    protected function stripHtmlTags(string $text): string
    {
        $text = preg_replace(
            [
                // Remove invisible content
                /** @lang php */
                '@<head[^>]*?>.*?</head>@siu',
                /** @lang php */
                '@<style[^>]*?>.*?</style>@siu',
                /** @lang php */
                '@<script[^>]*?.*?</script>@siu',
                /** @lang php */
                '@<object[^>]*?.*?</object>@siu',
                /** @lang php */
                '@<embed[^>]*?.*?</embed>@siu',
                /** @lang php */
                '@<applet[^>]*?.*?</applet>@siu',
                /** @lang php */
                '@<noframes[^>]*?.*?</noframes>@siu',
                /** @lang php */
                '@<noscript[^>]*?.*?</noscript>@siu',
                /** @lang php */
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ],
            [
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
            ],
            $text
        );

        return strip_tags($text);
    }
}
