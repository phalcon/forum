<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team and contributors               |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

use Helper\Post;
use Helper\User;
use Helper\Mail;
use Helper\Category;
use Phalcon\Events\Event;
use Phosphorum\Mail\SendNotifications;
use Phalcon\Ext\Mailer\Message;

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

    protected $fixturePath;

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
                $that->messages[current($message->getFrom()).current($message->getTo())] = [
                    'body'    => $message->getSwiftMessage()->getBody(),
                    'subject' => $message->getSubject(),
                    'from'    => $message->getFrom(),
                    'replyTo' => $message->getReplyTo(),
                    'to'      => $message->getTo(),
                    'mail'    => $message->getSwiftMessage()->toString(),
                ];

                return false;
            }
        );

        /** @var \Phalcon\Mailer\Manager $mailer */
        $mailer = $di->get('mailer');
        $mailer->setEventsManager($eventsManager);

        $this->fixturePath = container('registry')->offsetGet('tests_fixtures');
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

    /**
     * Tests SendNotifications. Create correct data from post
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.svyrydenko@gmail.com>
     * @since  2018-01-08
     */
    public function shouldCreateCorrectEmailFromPost(ConsoleTester $I)//++
    {
        $fixtureData = include $this->fixturePath . 'console/notification/data_to_check_post.php';

        $author = $this->user->haveUser([
            'karma'         => 401,
            'notifications' => 'N',
        ]);

        $guest = $this->user->haveUser([
            'karma' => 301,
            'notifications' => 'Y',
        ]);

        $catId  = $this->category->haveCategory();
        $postId = $this->post->havePost([
            'title'         => 'title <p>tag</p>',
            'slug'          => 'email-slug-post',
            'content'       => $fixtureData['post'],
            'users_id'      => $author['id'],
            'categories_id' => $catId,
        ]);

        $spool = new SendNotifications();
        $spool->sendRemaining();
        $config = container('config');

        $expected = [
            'title'         => 'title tag',
            'body'          => $fixtureData['html'],
            'post_id'       => $postId,
            'app_name'      => $config->mailer->from->name,
            'base_uri'      => $config->site->url,
            'slug'          => 'email-slug-post',
            'template_path' => 'mails/post_notification.html',
        ];

        if (!array_key_exists($author['name'].$guest['name'], $this->messages)) {
            $I->fail("Failed to test notifications.\n" . print_r($this->messages, true));
        }

        $notification = $this->messages[$author['name'].$guest['name']];

        //test data from mail
        $I->assertEquals("[{$config->site->name}] title tag", $notification['subject'], 'Email subject is wrong');
        $I->assertEquals([$config->mailer->from->email => $author['name']], $notification['from'], 'Email sender is wrong');
        $I->assertEquals([$guest['email'] => $guest['name']], $notification['to'], 'Email recipient is wrong');

        //test html part of mail
        $this->mail->seeHtmlBody($notification['body'], $expected);

        //test text/plain part of mail
        $I->assertEquals(
            1,
            substr_count($this->mail->getTextPartOfMailFromNotification($notification['mail']), $fixtureData['text']),
            'Text part of email is wrong'
        );
    }

    /**
     * Tests SendNotifications. Create correct data from reply
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.svyrydenko@gmail.com>
     * @since  2018-01-08
     */
    public function shouldCreateCorrectEmailFromReply(ConsoleTester $I)//+
    {
        $fixtureData = include $this->fixturePath . 'console/notification/data_to_check_reply.php';

        $author = $this->user->haveUser([
            'karma'         => 401,
            'notifications' => 'Y',
        ]);

        $catId  = $this->category->haveCategory();
        $postId = $this->post->havePost([
            'title'         => 'title <p>tag</p>',
            'slug'          => 'some-slug-here',
            'content'       => 'post content',
            'users_id'      => $author['id'],
            'categories_id' => $catId,
        ]);

        $guest = $this->user->haveUser([
            'karma' => 301,
        ]);

        $replyId = $this->post->havePostReply([
            'posts_id' => $postId,
            'users_id' => $guest['id'],
            'content'  => $fixtureData['reply'],
        ]);

        $spool = new SendNotifications();
        $spool->sendRemaining();
        $config = container('config');

        $expected = [
            'title'        => 'title tag',
            'body'         => $fixtureData['html'],
            'post_id'      => $postId,
            'reply_id'     => $replyId,
            'app_name'     => $config->mailer->from->name,
            'base_uri'     => $config->site->url,
            'slug'         => 'some-slug-here',
'           template_path' => 'mails/reply_notification.html', //added
        ];

        if (!array_key_exists($guest['name'] . $author['name'], $this->messages)) {
            $I->fail("Failed to test notifications.\n" . print_r($this->messages, true));
        }

        $notification = $this->messages[$guest['name'] . $author['name']];

        //test data from mail
        $I->assertEquals("[{$config->site->name}] title tag", $notification['subject']);
        $I->assertEquals([$config->mailer->from->email => $guest['name']], $notification['from']);
        $I->assertEquals([$author['email'] => $author['name']], $notification['to']);

        //test html part of mail
        $this->mail->seeHtmlBody($notification['body'], $expected);

        //test text/plain part of mail
        $I->assertEquals(
            1,
            substr_count($this->mail->getTextPartOfMailFromNotification($notification['mail']), $fixtureData['text']),
            'Text part of email is wrong'
        );

    }

    /**
     * Tests SendNotifications. Data shouldn't be createsd with empty content
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.svyrydenko@gmail.com>
     * @since  2018-01-08
     */
    public function shouldDoesntCreateEmailWithEmptyContent(ConsoleTester $I)//++
    {
        $author = $this->user->haveUser([
            'karma'         => 401,
            'notifications' => 'N',
        ]);

        $guest = $this->user->haveUser([
            'karma' => 301,
            'notifications' => 'Y',
        ]);

        $catId  = $this->category->haveCategory();
        $this->post->havePost([
            'title'         => 'title <p>tag</p>',
            'slug'          => 'email-slug-post',
            'content'       => '',
            'users_id'      => $author['id'],
            'categories_id' => $catId,
        ]);

        $spool = new SendNotifications();
        $spool->sendRemaining();

        $I->assertFalse(array_key_exists($author['name'].$guest['name'], $this->messages));
    }

    /**
     * Tests SendNotifications. Data shouldn't be createsd with incorrect receiver email
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.svyrydenko@gmail.com>
     * @since  2018-01-08
     */
    public function shouldDoesntCreateEmailWithIncorrectEmail(ConsoleTester $I)//++
    {
        $author = $this->user->haveUser([
            'karma'         => 401,
            'notifications' => 'N',
        ]);

        $guest = $this->user->haveUser([
            'karma' => 301,
            'notifications' => 'Y',
            'email'         => 'foob_bar.com',
        ]);

        $catId  = $this->category->haveCategory();
        $this->post->havePost([
            'title'         => 'title <p>tag</p>',
            'slug'          => 'email-slug-post',
            'content'       => 'some content',
            'users_id'      => $author['id'],
            'categories_id' => $catId,
        ]);

        $spool = new SendNotifications();
        $spool->sendRemaining();

        $I->assertFalse(array_key_exists($author['name'].$guest['name'], $this->messages));
    }

    /**
     * Tests SendNotifications. Data shouldn't be createsd with prohibition to send
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.svyrydenko@gmail.com>
     * @since  2018-01-08
     */
    public function shouldDoesntCreateEmailWithProhibitionToSend(ConsoleTester $I)//
    {
        $author = $this->user->haveUser([
            'karma'         => 401,
            'notifications' => 'N',
        ]);

        $guest = $this->user->haveUser([
            'karma' => 301,
            'notifications' => 'N',
        ]);

        $catId  = $this->category->haveCategory();
        $this->post->havePost([
            'title'         => 'title <p>tag</p>',
            'slug'          => 'email-slug-post',
            'content'       => 'some content',
            'users_id'      => $author['id'],
            'categories_id' => $catId,
        ]);

        $spool = new SendNotifications();
        $spool->sendRemaining();

        $I->assertFalse(array_key_exists($author['name'].$guest['name'], $this->messages));
    }
}
