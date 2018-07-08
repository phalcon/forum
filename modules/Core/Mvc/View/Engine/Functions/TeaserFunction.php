<?php
declare(strict_types=1);

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

namespace Phosphorum\Core\Mvc\View\Engine\Functions;

use Stringy\Stringy;

/**
 * Phosphorum\Core\Mvc\View\Engine\Functions\TeaserFunction
 *
 * @package Phosphorum\Core\Mvc\View\Engine\Functions
 */
class TeaserFunction
{
    /**
     * Truncates the text to a given length.
     *
     * @param  string $text
     * @param  int    $maxLen
     * @param  bool   $saveWords
     * @param  string $endWith
     *
     * @return string
     */
    public function __invoke(string $text, int $maxLen = 400, bool $saveWords = true, string $endWith = ' &hellip;')
    {
        $content = \preg_replace(
            '#<a[^>]+href="([^"]+)"[^>]*>([^<]+)<\/a>#',
            '$1 ',
            $text
        );

        $content = \strip_tags($content);
        $content = \str_replace('&nbsp;', ' ', $content);
        $content = \preg_replace('#\t+#', ' ', $content);
        $content = \preg_replace('# {2,}#', ' ', $content);
        $content = \preg_split('#(\r|\n)#', $content);

        $content = \implode(' ', \array_filter($content, function ($line) {
            return '' !== \trim($line);
        }));

        $content = \preg_replace('#^[ \t]+#m', ' ', $content);

        $string = new Stringy($content);
        $length = $string->length();

        if ($length <= $maxLen) {
            return (string) $string;
        }

        $string->trimRight('. ');

        if ($saveWords) {
            while ($maxLen < $length && \preg_match('/^\pL$/', (string) $string->substr($maxLen, 1))) {
                $maxLen++;
            }
        }

        return $string->substr(0, $maxLen) . $endWith;
    }
}
