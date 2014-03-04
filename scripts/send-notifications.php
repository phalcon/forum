<?php

/**
 * This scripts generates random posts
 */
require 'cli-bootstrap.php';

class SendSpoolTask extends Phalcon\DI\Injectable
{

	protected $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	private function _prerify($text)
	{
		if (preg_match_all('#```([a-z]+)(.+)```([\n\r]+)?#m', $text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$text = str_replace($match[0], '<pre>' . $match[2] . '</pre>', $text);
			}
		}
		return $text;
	}

	public function run()
	{
		$transport = null;
		foreach (Phosphorum\Models\Notifications::find('sent = "N"') as $notification) {

			$post = $notification->post;
			$user = $notification->user;

			if ($post && $user) {

				if ($user->email && $user->notifications != 'N') {

					$from = 'reply-i' . $post->id . '-' . time() . '@phosphorum.com';

					$message = new \Swift_Message('[Phalcon Forum] ' . $post->title);
					$message->setTo(array($user->email => $user->name));

					if ($notification->type == 'P') {
						$originalContent = $post->content;
						$escapedContent = $this->escaper->escapeHtml($post->content);
						$message->setFrom(array($from => $post->user->name));
					} else {
						$reply = $notification->reply;
						$originalContent = $reply->content;
						$escapedContent = $this->escaper->escapeHtml($reply->content);
						$message->setFrom(array($from => $reply->user->name));
					}

					if (trim($escapedContent)) {

						$prerifiedContent = $this->_prerify($escapedContent);
						$htmlContent = nl2br($prerifiedContent);

						$textContent = $originalContent;

						$htmlContent .= '<p style="font-size:small;-webkit-text-size-adjust:none;color:#717171;">';
						if ($notification->type == 'P') {
							$htmlContent .= '&mdash;<br>Reply to this email directly or view the complete thread on '.
							PHP_EOL . '<a href="http://phosphorum.com/discussion/' . $post->id. '/' . $post->slug . '">Phosphorum</a>. ';
						} else {
							$htmlContent .= '&mdash;<br>Reply to this email directly or view the complete thread on '.
							PHP_EOL . '<a href="http://phosphorum.com/discussion/' . $post->id. '/' . $post->slug . '#C' . $reply->id . '">Phosphorum</a>. ';
						}
						$htmlContent .= PHP_EOL . 'Change your e-mail preferences <a href="http://phosphorum.com/settings">here</a></p>';

						$bodyMessage = new \Swift_MimePart($htmlContent, 'text/html');
						$bodyMessage->setCharset('UTF-8');
						$message->attach($bodyMessage);

						$bodyMessage = new \Swift_MimePart($textContent, 'text/plain');
						$bodyMessage->setCharset('UTF-8');
						$message->attach($bodyMessage);

						if (!$transport) {

							$transport = \Swift_SmtpTransport::newInstance(
								$this->config->smtp->host,
								$this->config->smtp->port,
								$this->config->smtp->security
							);
							$transport->setUsername($this->config->smtp->username);
							$transport->setPassword($this->config->smtp->password);

							$mailer = \Swift_Mailer::newInstance($transport);
						}

						$mailer->send($message);
					}
				}

				$notification->sent = 'Y';
				if ($notification->save() == false) {
					foreach ($notification->getMessages() as $message) {
						echo $message->getMessage(), PHP_EOL;
					}
				}
			}
		}
	}

}

try {
	$task = new SendSpoolTask($config);
	$task->run();
} catch(Exception $e) {
	echo $e->getMessage(), PHP_EOL;
	echo $e->getTraceAsString();
}
