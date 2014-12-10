<?php

namespace Phosphorum\Markdown;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Markdown;
use Phalcon\DI\Injectable;

/**
 * Class MentionExtension
 *
 * @package Phosphorum\Markdown
 */
class MentionExtension extends Injectable implements ExtensionInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $markdown->on('inline', [$this, 'processMentions']);
    }

    /**
     * @param Text $text
     */
    public function processMentions(Text $text)
    {
        // Turn @username into [@username](http://example.com/user/username)
        $text->replace('/(?:^|[^a-zA-Z0-9.])@([A-Za-z]+[A-Za-z0-9]+)/', function (Text $w, Text $username) {
            return ' [@' . $username . '](' . $this->config->site->url . '/user/0/' . $username . ')';
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mention';
    }
}
