<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
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

use Phalcon\Di\Injectable;
use Phosphorum\Models\Posts;
use Phosphorum\Models\Users;

/**
 * Digest
 *
 * Sends a weekly digest to subscribed users
 */
class Digest extends Injectable
{

    protected $transport;

    protected $mailer;

    /**
     * Sends the digest
     */
    public function send()
    {
        $lastMonths = new \DateTime();
        $lastMonths->modify('-6 month');

        $parameters = array(
            'modified_at >= ?0 AND digest = "Y" AND notifications <> "N"',
            'bind'  => array($lastMonths->getTimestamp())
        );

        $users = array();
        foreach (Users::find($parameters) as $user) {
            if ($user->email && strpos($user->email, '@') !== false && strpos($user->email, '@users.noreply.github.com') === false) {
                $users[trim($user->email)] = $user->name;
            }
        }

        $fromName  = $this->config->mail->fromName;
        $fromEmail = $this->config->mail->fromEmail;
        $url       = $this->config->site->url;

        $subject = 'Top Stories from Phosphorum ' . date('d/m/y');

        $lastWeek = new \DateTime();
        $lastWeek->modify('-1 week');

        $parameters = array(
            'created_at >= ?0 AND deleted != 1 AND categories_id <> 4',
            'bind'  => array($lastWeek->getTimestamp()),
            'order' => 'number_views + ((IF(votes_up IS NOT NULL, votes_up, 0) - IF(votes_down IS NOT NULL, votes_down, 0)) * 4) + number_replies + IF(accepted_answer = "Y", 10, 0) DESC',
            'limit' => 10
        );

        $e = $this->escaper;

        $content = '<html><head></head><body><p><h1 style="font-size:22px;color:#333;letter-spacing:-0.5px;line-height:1.25;font-weight:normal;padding:16px 0;border-bottom:1px solid #e2e2e2">Top Stories from Phosphorum</h1></p>';

        foreach (Posts::find($parameters) as $post) {

            $user = $post->user;
            if ($user == false) {
                continue;
            }

            $content .= '<p><a style="text-decoration:none;display:block;font-size:20px;color:#333;letter-spacing:-0.5px;line-height:1.25;font-weight:normal;color:#155fad" href="'. $url .'/discussion/' . $post->id . '/' . $post->slug . '">' . $e->escapeHtml($post->title) . '</a></p>';

            $content .= '<p><table width="100%"><td><table><tr><td>' .
                        '<img src="https://secure.gravatar.com/avatar/' . $user->gravatar_id . '?s=32&amp;r=pg&amp;d=identicon" width="32" height="32" alt="' . $user->name . ' icon">' .
                        '</td><td><a style="text-decoration:none;color:#155fad" href="' . $url . '/user/' . $user->id . '/' . $user->login . '">' . $user->name . '<br><span style="text-decoration:none;color:#999;text-decoration:none">' . $user->getHumanKarma() . '</span></a></td></tr></table></td><td align="right"><table style="border: 1px solid #dadada;" cellspacing=5>' .
                        '<td align="center"><label style="color:#999;margin:0px;font-weight:normal;">Created</label><br>' . $post->getHumanCreatedAt() . '</td>' .
                        '<td align="center"><label style="color:#999;margin:0px;font-weight:normal;">Replies</label><br>' . $post->number_replies . '</td>' .
                        '<td align="center"><label style="color:#999;margin:0px;font-weight:normal;">Views</label><br>' . $post->number_views . '</td>' .
                        '<td align="center"><label style="color:#999;margin:0px;font-weight:normal;">Votes</label><br>' . ($post->votes_up - $post->votes_down) . '</td>' .
                        '</tr></table></td></tr></table></p>';

            $content .= $this->markdown->render($e->escapeHtml($post->content));

            $content .= '<p><a style="color:#155fad" href="' . $url . '/discussion/' . $post->id . '/' . $post->slug . '">Read more</a></p>';

            $content .= '<hr style="border: 1px solid #dadada">';

        }

        $textContent = strip_tags($content);

        $htmlContent = $content . '<p style="font-size:small;-webkit-text-size-adjust:none;color:#717171;">';
        $htmlContent .= PHP_EOL . 'This email was sent by Phalcon Framework. Change your e-mail preferences <a href="' . $url . '/settings">here</a></p>';

        foreach ($users as $email => $name) {

            try {

                $message = new \Swift_Message('[Phalcon Forum] ' . $subject);
                $message->setTo(array($email => $name));
                $message->setFrom(array($fromEmail => $fromName));

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

            } catch (\Exception $e) {
                echo $e->getMessage(), PHP_EOL;
            }
        }

    }
}
