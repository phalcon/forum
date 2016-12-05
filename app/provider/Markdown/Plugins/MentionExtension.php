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

namespace Phosphorum\Provider\Markdown\Plugins;

use Ciconia\Markdown;
use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

/**
 * Phosphorum\Provider\Markdown\Plugins\MentionExtension
 *
 * @package Phosphorum\Provider\Markdown\Plugins
 */
class MentionExtension implements ExtensionInterface
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
        $text->replace(
            '/(?:^|[^a-zA-Z0-9.])@([A-Za-z0-9]+)/',
            function (Text $w, Text $username) {
                $url = container('config')->site->url;

                return ' [@' . $username . '](' . rtrim($url, '/') . '/user/0/' . $username . ')';
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mention';
    }
}
