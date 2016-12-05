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
 * Phosphorum\Provider\Markdown\Plugins\UnderscoredUrlsExtension
 *
 * Turn standard URL into markdown URL (http://example.com -> <http://example.com>)
 *
 * @author  Kazuyuki Hayashi <hayashi@valnur.net>
 * @package Phosphorum\Provider\Markdown\Plugins
 */
class UrlAutoLinkExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $markdown->on('inline', [$this, 'processStandardUrl'], 35);
    }

    /**
     * Turn standard URL into markdown URL
     *
     * @param Text $text
     */
    public function processStandardUrl(Text $text)
    {
        $hashes = [];

        // escape <code>
        $text->replace('{<code>.*?</code>}m', function (Text $w) use (&$hashes) {
            $md5 = md5($w);
            $hashes[$md5] = $w;

            return "{gfm-extraction-$md5}";
        });

        $text->replace('{(?<!]\(|"|<|\[)((?:https?|ftp)://[^\'"\)>\s]+)(?!>|\"|\])}', '<\1>');

        /** @noinspection PhpUnusedParameterInspection */
        $text->replace('/\{gfm-extraction-([0-9a-f]{32})\}/m', function (Text $w, Text $md5) use (&$hashes) {
            return $hashes[(string)$md5];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'urlAutoLink';
    }
}
