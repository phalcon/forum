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
 * Underscored URLs Extension
 *
 * @package Phosphorum\Provider\Markdown\Plugins
 */
class UnderscoredUrlsExtension implements ExtensionInterface
{
    public function register(Markdown $markdown)
    {
        $markdown->on('inline', [$this, 'processTest']);
    }

    public function processTest(Text $text)
    {
        $hashes = [];

        // escape <code>
        $text->replace('{<code>.*?</code>}m', function (Text $w) use (&$hashes) {
            $md5 = md5($w);
            $hashes[$md5] = $w;

            return "{gfm-extraction-$md5}";
        });

        $pattern = '#(?:(?<=[href|src]=\"|\')(?:[a-z0-9]+:\/\/)?|(?:[a-z0-9]+:\/\/))([^\'">\s]+_+[^\'">\s]*)+#i';
        $text->replace($pattern, function (Text $w) {

            $w->replaceString('_', '%5');

            return $w;
        });

        /** @noinspection PhpUnusedParameterInspection */
        $text->replace('/\{gfm-extraction-([0-9a-f]{32})\}/m', function (Text $w, Text $md5) use (&$hashes) {
            return $hashes[(string)$md5];
        });
    }

    public function getName()
    {
        return 'underscoredUrls';
    }
}
