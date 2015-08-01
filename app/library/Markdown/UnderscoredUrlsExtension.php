<?php

namespace Phosphorum\Markdown;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Markdown;

/**
 * Underscored URLs Extension
 *
 * @package Phosphorum\Markdown
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

        $text->replace('#(?:(?<=[href|src]=\"|\')(?:[a-z0-9]+:\/\/)?|(?:[a-z0-9]+:\/\/))([^\'">\s]+_+[^\'">\s]*)+#i', function (Text $w) {

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
