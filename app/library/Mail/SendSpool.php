<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Mail;

use Phalcon\Tag;
use Phosphorum\Models\Notifications;
use Phalcon\Di\Injectable;

/**
 * SendSpool
 *
 * Sends e-mails to users in the spool
 */
class SendSpool extends Injectable
{
    protected $transport;

    protected $mailer;

    public function send(Notifications $notification)
    {
        if ($notification->sent == 'Y') {
            return;
        }

        $post = $notification->post;
        $user = $notification->user;
        if ($notification->type != 'P') {
            $reply = $notification->reply;
        } else {
            $reply = true;
        }

        $from = $this->config->mail->fromEmail;
        $url  = rtrim($this->config->site->url, '/');

        if ($post && $user && $reply) {
            $isGitHubEmail = strpos($user->email, '@users.noreply.github.com');
            if ($user->email && $user->notifications != 'N' && false === $isGitHubEmail) {
                try {
                    $message = new \Swift_Message("[{$this->config->site->name} Forum] " . $post->title);
                    $message->setTo(array($user->email => $user->name));
                    $message->addReplyTo('reply-i' . $post->id . '-' . time() . '@phosphorum.com');

                    $e = $this->escaper;

                    if ($notification->type == 'P') {
                        $originalContent = $post->content;
                        $htmlContent = $this->markdown->render($e->escapeHtml($post->content));
                        $message->setFrom(array($from => $post->user->name));
                    } else {
                        $reply = $notification->reply;
                        $originalContent = $reply->content;
                        $htmlContent = $this->markdown->render($e->escapeHtml($reply->content));
                        $message->setFrom(array($from => $reply->user->name));
                    }

                    if (trim($originalContent)) {
                        $textContent = strip_tags($originalContent);

                        $htmlContent .= '<p style="font-size:small;-webkit-text-size-adjust:none;color:#717171;">';
                        $href = "{$url}/discussion/{$post->id}/{$post->slug}";
                        $title = $this->config->site->name;
                        $link = function ($href) use ($title) {
                            return Tag::linkTo([$href, $title, "local" => false]);
                        };

                        if ($notification->type == 'P') {
                            $link = $link($href);
                        } else {
                            $link = $link($href . '#C' . $reply->id);
                        }

                        $htmlContent .= '&mdash;<br>Reply to this email directly or view the complete thread on ' .
                            PHP_EOL . $link .
                            PHP_EOL . 'Change your e-mail preferences <a href="'. $url . '/settings">here</a></p>';

                        $bodyMessage = new \Swift_MimePart($htmlContent, 'text/html');
                        $bodyMessage->setCharset('UTF-8');
                        $message->attach($bodyMessage);

                        $bodyMessage = new \Swift_MimePart($textContent, 'text/plain');
                        $bodyMessage->setCharset('UTF-8');
                        $message->attach($bodyMessage);

                        if (!$this->transport) {
                            $this->transport = \Swift_SmtpTransport::newInstance(
                                $this->config->smtp->host,
                                $this->config->smtp->port,
                                $this->config->smtp->security
                            );
                            $this->transport->setUsername($this->config->smtp->username);
                            $this->transport->setPassword($this->config->smtp->password);
                        }

                        if (!$this->mailer) {
                            $this->mailer = \Swift_Mailer::newInstance($this->transport);
                        }

                        $this->mailer->send($message);
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage(), PHP_EOL;
                }
            }
        }

        $notification->sent = 'Y';
        if ($notification->save() == false) {
            foreach ($notification->getMessages() as $message) {
                echo $message->getMessage(), PHP_EOL;
            }
        }
    }

    /**
     * Check notifications marked as not send on the databases and send them
     */
    public function sendRemaining()
    {
        foreach (Notifications::find('sent = "N"') as $notification) {
            $this->send($notification);
        }
    }

    /**
     * Check the queue from Beanstalk and send the notifications scheduled there
     */
    public function consumeQueue()
    {
        while (true) {
            while ($this->queue->peekReady() !== false) {
                $job = $this->queue->reserve();

                $message = $job->getBody();

                foreach ($message as $userId => $id) {
                    $notification = Notifications::findFirstById($id);
                    if ($notification) {
                        $this->send($notification);
                    }
                }

                if (is_object($this->transport)) {
                    $this->transport->stop();
                    $this->transport = null;
                    $this->mailer = null;
                }

                $job->delete();
            }

            sleep(5);
        }
    }
}
