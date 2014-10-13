<?php

namespace Phosphorum\Markdown;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Markdown;

/**
 * Turn standard URL into markdown URL (http://example.com -> <http://example.com>)
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class UrlAutoLinkExtension implements ExtensionInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $markdown->on('inline', array($this, 'processStandardUrl'), 35);
    }

    /**
     * Turn standard URL into markdown URL
     *
     * @param Text $text
     */
    public function processStandardUrl(Text $text)
    {
        $hashes = array();

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
