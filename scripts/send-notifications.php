<?php

/**
 * This scripts generates random posts
 */
require 'cli-bootstrap.php';
require '../vendor/AWSSDKforPHP/sdk.class.php';
require '../vendor/Swift/Swift.php';

class SendSpoolTask extends Phalcon\DI\Injectable
{

	protected $_amazonSes;

	protected $_config;

	public function __construct($config)
	{
		$this->_config = $config;
	}

	private function _amazonSESSend($raw)
	{

		if ($this->_amazonSes == null) {
			$this->_amazonSes = new AmazonSES($this->_config->amazon->AWSAccessKeyId, $this->_config->amazon->AWSSecretKey);
			$this->_amazonSes->disable_ssl_verification();
		}

		$response = $this->_amazonSes->send_raw_email(array(
			'Data' => base64_encode($raw)
		), array(
			'curlopts' => array(
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0
			)
		));

		if (!$response->isOK()) {
			echo 'Error sending email from AWS SES: ' . $response->body->asXML(), PHP_EOL;
			return false;
		}

		return $response->body;
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
		foreach (Phosphorum\Models\Notifications::find('sent = "N"') as $notification) {

			$post = $notification->post;
			$user = $notification->user;

			if ($user->email && $user->notifications != 'N') {

				$message = new Swift_Message('[Phalcon Forum] ' . $this->escaper->escapeHtml($post->title));
				$message->setTo(new Swift_Address($user->email, $user->name));

				if ($notification->type == 'P') {
					$escapedContent = $this->escaper->escapeHtml($post->content);
					$message->setFrom(new Swift_Address('phosphorum@phalconphp.com', $post->user->name));
				} else {
					$reply = $notification->reply;
					$escapedContent = $this->escaper->escapeHtml($reply->content);
					$message->setFrom(new Swift_Address('phosphorum@phalconphp.com', $reply->user->name));
				}

				$prerifiedContent = $this->_prerify($escapedContent);
				$htmlContent = nl2br($prerifiedContent);

				$textContent = $escapedContent;

				$htmlContent .= '<p style="font-size:small;-webkit-text-size-adjust:none;color:#717171;">';
				if ($notification->type == 'P') {
					$htmlContent .= '&mdash;<br>View the complete thread on '.
					PHP_EOL.'<a href="http://forum.phalconphp.com/discussion/' .$post->id. '/' . $post->slug . '">Phosphorum</a>. ';
				} else {
					$htmlContent .= '&mdash;<br>View the complete thread on '.
					PHP_EOL.'<a href="http://forum.phalconphp.com/discussion/' .$post->id. '/' . $post->slug . '#C' . $reply->id . '">Phosphorum</a>. ';
				}
				$htmlContent .= PHP_EOL.'Change your preferences <a href="http://forum.phalconphp.com/settings">here</a>';

				$bodyMessage = new Swift_Message_Part($htmlContent, 'text/html');
				$bodyMessage->setCharset('UTF-8');
				$message->attach($bodyMessage);

				$bodyMessage = new Swift_Message_Part($textContent, 'text/plain');
				$bodyMessage->setCharset('UTF-8');
				$message->attach($bodyMessage);

				$raw = '';
				$data = $message->build();
				while (false !== $bytes = $data->read()){
					$raw .= $bytes;
				}

				if (($sendResponse = $this->_amazonSESSend($raw)) !== false) {
					$notification->message_id = (string) $sendResponse->SendRawEmailResult->MessageId;
				}
			}

			$notification->sent = 'Y';
			if ($notification->save()==false) {
				foreach ($notification->getMessages() as $message) {
					echo $message->getMessage(), PHP_EOL;
				}
			}
		}
	}

}

try {
	$task = new SendSpoolTask($config);
	$task->run();
} catch(Exception $e) {
	echo $e->getTraceAsString();
}
