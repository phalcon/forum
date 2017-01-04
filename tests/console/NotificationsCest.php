<?php

use Helper\Post;
use Helper\User;
use Helper\Mail;
use Helper\Category;
use Phalcon\Events\Event;
use Phalcon\Mailer\Message;
use Phosphorum\Mail\SendSpool;

class NotificationsCest
{
    /** @var Category */
    protected $category;

    /** @var User */
    protected $user;

    /** @var Post */
    protected $post;

    /** @var Mail */
    protected $mail;

    protected $messages = [];

    public function _before(ConsoleTester $I)
    {
        /** @var \Phalcon\Di\FactoryDefault $di */
        $di = $I->getApplication()->getDI();

        /** @var \Phalcon\Events\Manager $eventsManager */
        $eventsManager = $di->get('eventsManager');

        $that = $this;

        $eventsManager->attach(
            'mailer:beforeSend',
            function (Event $event, Message $message, array $data = null) use ($that) {
                $swiftMessage = $message->getMessage();

                $that->messages[] = [
                    'body'    => $swiftMessage->getBody(),
                    'subject' => $swiftMessage->getSubject(),
                    'from'    => $swiftMessage->getFrom(),
                    'replyTo' => $swiftMessage->getReplyTo(),
                    'to'      => $swiftMessage->getTo(),
                ];

                return false;
            }
        );

        /** @var \Phalcon\Mailer\Manager $mailer */
        $mailer = $di->get('mailer');
        $mailer->setEventsManager($eventsManager);
    }

    public function _after(ConsoleTester $I)
    {
        $this->messages = [];
    }

    protected function _inject(Category $category, User $user, Post $post, Mail $mail)
    {
        $this->user     = $user;
        $this->post     = $post;
        $this->category = $category;
        $this->mail     = $mail;
    }

    // tests
    public function checkNotifications(ConsoleTester $I)
    {
        $author = $this->user->haveUser([
            'karma'         => 401,
            'notifications' => 'Y',
        ]);

        $catId  = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'some-title-here',
            'slug'          => 'some-slug-here',
            'content'       => 'some-content-here',
            'users_id'      => $author['id'],
            'categories_id' => $catId,
        ]);

        $guest = $this->user->haveUser([
            'karma' => 301,
        ]);

        $replyId = $this->post->havePostReply([
            'posts_id' => $postId,
            'users_id' => $guest['id'],
            'content'  => 'comment',
        ]);

        $spool = new SendSpool();
        $spool->sendRemaining();

        $config = container('config');

        $expected = [
            'title'    => 'some-title-here',
            'body'     => '<p>comment</p>',
            'post_id'  => $postId,
            'reply_id' => $replyId,
            'app_name' => $config->mailer->from->name,
            'base_uri' => $config->site->url,
            'slug'     => 'some-slug-here',
        ];

        $message = $this->messages[0];

        $this->mail->seeHtmlBodyForReply($message['body'], $expected);

        $I->assertEquals("[{$config->mailer->from->name} Forum] some-title-here", $message['subject']);
        $I->assertEquals([$config->mailer->from->email => $guest['name']], $message['from']);
        $I->assertEquals([$author['email'] => $author['name']], $message['to']);
    }
}
