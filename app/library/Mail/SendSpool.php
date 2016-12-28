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

use Phalcon\Di\Injectable;
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

        $params['title'] = "[{$config->site->name} Forum] {$post->title}";

        if (!$contents = $this->prepareContent('mail/notification', $params)) {
            $notificationService->markAsInvalid($notification);
            return;
        }

        $message = $mailer->createMessage()
            ->to($user->email, $user->name)
            ->subject($params['title'])
            ->content($contents);

        $message->replyTo("reply-i{$post->id}-" . time() . '@phosphorum.com');
        $message->from($notificationService->getFromUser($notification));

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

        return compact('html_content', 'post_url', 'settings_url');
    }
}
