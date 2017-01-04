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

namespace Phosphorum\Mail;

use DOMDocument;
use Phalcon\Di\Injectable;
use Phalcon\Mailer\Message;
use Phosphorum\Model\Notifications;
use Phosphorum\Model\Services\Service;

/**
 * SendSpool
 *
 * Sends e-mails to users in the spool
 */
class SendSpool extends Injectable
{
    /**
     * Check notifications marked as not send on the databases and send them
     */
    public function sendRemaining()
    {
        $notifications = Notifications::find([
            'conditions' => 'sent = ?1',
            'bind'       => [1 => Notifications::STATUS_NOT_SENT],
        ]);

        foreach ($notifications as $notification) {
            $this->send($notification);
        }
    }

    /**
     * Check the queue from Beanstalk and send the notifications scheduled there
     */
    public function consumeQueue()
    {
        while (true) {
            while (container('queue')->peekReady() !== false) {
                $job = container('queue')->queue->reserve();

                $message = $job->getBody();

                foreach ($message as $userId => $id) {
                    if ($notification = Notifications::findFirstById($id)) {
                        $this->send($notification);
                    }
                }

                $job->delete();
            }

            sleep(5);
        }
    }

    protected function send(Notifications $notification)
    {
        /**
         * @var Service\Notifications $notificationService
         * @var Service\Users         $userService
         */

        $notificationService = container(Service\Notifications::class);
        $userService = container(Service\Users::class);

        if (!$notificationService->isReadyToBeSent($notification)) {
            return;
        }

        if (!$notificationService->hasRequiredIntegrity($notification)) {
            $notificationService->markAsInvalid($notification);
            return;
        }

        $post = $notification->post;
        $user = $notification->user;

        /** @var \Phosphorum\Email\EmailComponent $email */
        $email = singleton('email', [$user->email, false]);

        if (!$email->valid()) {
            $notificationService->markAsSkipped($notification);
            return;
        }

        if (!$userService->doesExpectNotifications($user)) {
            $notificationService->markAsSkipped($notification);
            return;
        }

        if (!$params = $this->prepareContentParams($notification)) {
            $notificationService->markAsInvalid($notification);
            return;
        }

        /** @var \Phalcon\Mailer\Manager $mailer */
        $mailer = container('mailer');
        $config = container('config');

        $params['subject'] = "[{$config->site->name} Forum] {$post->title}";

        if (!$contents = $this->prepareContent('mail/notification', $params)) {
            $notificationService->markAsInvalid($notification);
            return;
        }

        $message = $mailer->createMessage()
            ->to($user->email, $user->name)
            ->subject($params['subject'])
            ->content($contents, Message::CONTENT_TYPE_HTML, 'utf-8');

        $plain = $this->preparePlainTextFromHtml($contents);

        $message->getMessage()->addPart($plain, Message::CONTENT_TYPE_PLAIN, 'utf-8');
        $message->replyTo("reply-i{$post->id}-" . time() . '@phosphorum.com');
        $message->from($notificationService->getFromUser($notification));

        $this->prepareMessageId($message, $notification);

        $message->send();

        $notificationService->markAsCompleted($notification);
    }

    /**
     * Prepare mail content.
     *
     * @param string     $viewPath
     * @param array|null $params
     *
     * @return string
     */
    protected function prepareContent($viewPath, array $params = null)
    {
        $view = container('view');

        ob_start();

        $view->render($viewPath, $params);

        ob_end_clean();

        return (string) $view->getContent();
    }

    protected function prepareContentParams(Notifications $notification)
    {
        /** @var Service\Notifications $notificationService */
        $notificationService = container(Service\Notifications::class);

        $contents = $notificationService->getContentsForNotification($notification);

        if (!trim($contents)) {
            return null;
        }

        $html_content = container('markdown')->render(container('escaper')->escapeHtml($contents));
        $post_url     = $notificationService->getRelatedPostUrl($notification);
        $settings_url = container('config')->site->url . '/settings';
        $title        = container('escaper')->escapeHtml($notification->post->title);

        return compact('html_content', 'post_url', 'settings_url', 'title');
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
     * @param  Notifications $notification
     * @return string
     */
    protected function prepareMessageId(Message $message, Notifications $notification)
    {
        $msgId  = $message->getMessage()->getHeaders()->get('Message-ID');
        $parsed = parse_url(container('config')->site->url);

        $id = sprintf(
            '%d.%d.%d.%d@%s',
            str_replace('.', '', microtime(true)),
            $notification->id,
            $notification->posts_id,
            $notification->users_id,
            isset($parsed['host']) ? $parsed['host'] : 'phosphorum.com'
        );

        $msgId->setId($id);

        return $id;
    }

    /**
     * Prepares and returns plain text from HTML part.
     *
     * @param  string $html
     * @return string
     */
    protected function preparePlainTextFromHtml($html)
    {
        if (!is_string($html) || empty($html)) {
            return '1';
        }

        $dom = new DOMDocument();
        if (!$dom->loadHTML($html)) {
            return '2';
        }

        $body   = $dom->getElementsByTagName('body');
        $cloned = $body->item(0)->cloneNode(true);

        $newDoc = new DOMDocument();
        $newDoc->appendChild($newDoc->importNode($cloned, true));
        $html = $newDoc->saveHTML();

        $m = preg_replace('#<a[^>]+href="([^"]+)"[^>]*>([^<]+)<\/a>#', '$2:' . "\n" . '$1', $html);
        $m = trim($this->stripHtmlTags($m));
        $m = str_replace('&nbsp;', ' ', $m);
        $m = preg_replace('#\t+#', '', $m);
        $m = preg_replace('# {2,}#', ' ', $m);
        $m = preg_replace('#(\r\n|\r|\n){2,}#m', "\n\n", $m);

        $m = explode("\n\n", $m);

        $text  = [];
        foreach ($m as $n => $line) {
            $line = trim($line);
            if (!empty($line)) {
                $text[] = $line;
            }
        }

        $m = implode("\n\n", $text);
        $m = preg_replace('#^[ \t]+#m', '', $m);
        $m = str_replace('&mdash;', '--', $m);

        return $m;
    }

    /**
     * Remove HTML tags, including invisible text such as style and
     * script code, and embedded objects.  Add line breaks around
     * block-level tags to prevent word joining after tag removal.
     *
     * @param  string $text
     * @return string
     */
    protected function stripHtmlTags($text)
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
