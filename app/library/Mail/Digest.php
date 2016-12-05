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
use Phosphorum\Model\Posts;
use Phosphorum\Model\Users;
use Phalcon\Mvc\View\Simple as View;

/**
 * Weekly Digest
 *
 * Sends a weekly digest to subscribed users
 *
 * @property \Phalcon\Avatar\Gravatar gravatar
 * @property \Phalcon\Config config
 * @property \Ciconia\Ciconia markdown
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

        $parameters = [
            'modified_at >= ?0 AND digest = "Y" AND notifications <> "N"',
            'bind'  => [$lastMonths->getTimestamp()]
        ];

        $users = [];
        foreach (Users::find($parameters) as $user) {
            if ($user->email &&
                strpos($user->email, '@') !== false &&
                strpos($user->email, '@users.noreply.github.com') === false
            ) {
                $users[trim($user->email)] = $user->name;
            }
        }

        $view = new View();
        $view->setViewsDir($this->config->application->viewsDir);

        $fromName  = $this->config->mail->fromName;
        $fromEmail = $this->config->mail->fromEmail;
        $url       = rtrim($this->config->site->url, '/');

        $subject = sprintf('Top Stories from Phosphorum %s', date('d/m/y'));
        $view->setVar('title', $subject);

        $lastWeek = new \DateTime();
        $lastWeek->modify('-1 week');

        $order = 'number_views + ' .
            '((IF(votes_up IS NOT NULL, votes_up, 0) - ' .
            'IF(votes_down IS NOT NULL, votes_down, 0)) * 4) + ' .
            'number_replies + IF(accepted_answer = "Y", 10, 0) DESC';
        $parameters = [
            'created_at >= ?0 AND deleted != 1 AND categories_id <> 4',
            'bind'  => [$lastWeek->getTimestamp()],
            'order' => $order,
            'limit' => 10
        ];

        $e = $this->escaper;
        /** @var \Phalcon\Logger\AdapterInterface $logger */
        $logger = $this->getDI()->get('logger', ['mail']);

        $stories = [];
        foreach (Posts::find($parameters) as $i => $post) {
            $user = $post->user;
            if ($user == false) {
                continue;
            }

            $this->gravatar->setSize(32);

            $stories[$i]['user_name']    = $user->name;
            $stories[$i]['user_avatar']  = $this->gravatar->getAvatar($user->email);
            $stories[$i]['user_url']     = "{$url}/user/{$user->id}/{$user->login}";
            $stories[$i]['user_karma']   = $user->getHumanKarma();
            $stories[$i]['post_title']   = $e->escapeHtml($post->title);
            $stories[$i]['post_created'] = $post->getHumanCreatedAt();
            $stories[$i]['post_replies'] = (int) $post->number_replies;
            $stories[$i]['post_views']   = (int) $post->number_views;
            $stories[$i]['post_votes']   = ($post->votes_up - $post->votes_down);
            $stories[$i]['post_content'] = $this->markdown->render($e->escapeHtml($post->content));
            $stories[$i]['post_url']     = "{$url}/discussion/{$post->id}/{$post->slug}";
        }

        if (empty($stories)) {
            return;
        }

        $view->setVar('stories', $stories);
        $view->setVar('notice', sprintf(
            'This email was sent by %s mail sender. Change your e-mail preferences <a href="%s/settings">here</a>',
            $this->config->site->name,
            $url
        ));

        $content = $view->render('mail/digest.phtml');

        $textContent = preg_replace('#<a[^>]+href="([^"]+)"[^>]*>([^<]+)<\/a>#', '$2:' . "\n" . '$1', $content);
        $textContent = str_replace('<span class="foot-line"></span>', "--\n", $textContent);
        $textContent = trim(strip_tags($textContent));
        $textContent = str_replace('&nbsp;', ' ', $textContent);
        $textContent = preg_replace('#\t+#', '', $textContent);
        $textContent = preg_replace('# {2,}#', ' ', $textContent);
        $textContent = preg_split('#(\r|\n)#', $textContent);
        $textContent = join("\n\n", array_filter($textContent, function ($line) {
            return '' !== trim($line);
        }));
        $textContent = preg_replace('#^[ \t]+#m', '', $textContent);

        foreach ($users as $email => $name) {
            try {
                $message = new \Swift_Message("[{$this->config->site->name} Forum] " . $subject);
                $message->setTo([$email => $name]);
                $message->setFrom([$fromEmail => $fromName]);

                $bodyMessage = new \Swift_MimePart($content, 'text/html');
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

                $failedRecipients = [];
                $this->mailer->send($message, $failedRecipients);

                if (empty($failedRecipients)) {
                    $logger->info("Sent an email to {$email}");
                } else {
                    $logger->error("Unable to sent an email to " . join(', ', $failedRecipients));
                }
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
                throw new \Exception($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
}
