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

namespace Phosphorum\Core\Markdown;

use ParsedownExtra;

/**
 * Phosphorum\Core\Markdown\Parsedown
 *
 * @package Phosphorum\Core\Markdown
 */
class Parsedown extends ParsedownExtra
{
    protected $extraInlineTypes = [
        '@' => ['UrlMentions'],
        '~' => ['InsTags'],
    ];

    protected $extraInlineMarkerList = '@~';

    /**
     * Parsedown constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->InlineTypes = array_merge($this->InlineTypes, $this->extraInlineTypes);
        $this->inlineMarkerList .= $this->extraInlineMarkerList;
    }

    /**
     * Adds <del> tag to HTML tree.
     *
     * Using in editor:
     * Foo ~Some code~ Bar
     *
     * Result in HTML tree:
     * Foo <del>Some code</del> Bar
     *
     * @param  array $excerpt
     * @return array|null
     */
    protected function inlineStrikethrough($excerpt)
    {
        if (isset($excerpt['text']) == false) {
            return null;
        }

        $wasMatched = preg_match('/^~{1}([^~].*?)~{1}/', $excerpt['text'], $matches);

        if ($wasMatched == false) {
            return null;
        }

        return [
            'extent' => strlen($matches[0]),
            'element' => [
                'name' => 'del',
                'text' => $matches[1],
            ],
        ];
    }

    /**
     * Render text.
     *
     * @param  string $text
     * @return string
     */
    public function render(string $text): string
    {
        return $this->text($text);
    }

    /**
     * Extension makes from '@12345' to `/user/0/12345`.
     *
     * @param  array $excerpt
     * @return array|null
     */
    protected function inlineUrlMentions(array $excerpt): ?array
    {
        if (isset($excerpt['context']) == false) {
            return null;
        }

        $wasMatched = preg_match(
            '/(?:^|[^a-zA-Z0-9.])@([A-Za-z0-9^\-\_]+)/',
            $excerpt['context'],
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if ($wasMatched == false) {
            return null;
        }

        return [
            'extent' => strlen($matches[0][0]),
            'position' => $matches[0][1],
            'element' => [
                'name' => 'a',
                'text' => $matches[0][0],
                'attributes' => [
                    'href' => '/user/0/' . $matches[1][0],
                ],
            ],
        ];
    }

    /**
     * Adds <ins> tag to HTML tree.
     *
     * Using in editor:
     * Foo ~~Some code~~ Bar
     *
     * Result in HTML tree:
     * Foo <ins>Some code</ins> Bar
     *
     * @param  array $excerpt
     * @return array|null
     */
    protected function inlineInsTags(array $excerpt): ?array
    {
        if (isset($excerpt['text']) == false) {
            return null;
        }

        $wasMatched = preg_match('/^~{2}(.*?)~{2}/', $excerpt['text'], $matches);

        if ($wasMatched == false) {
            return null;
        }

        return [
            'extent' => strlen($matches[0]),
            'element' => [
                'name' => 'ins',
                'text' => $matches[1],
            ],
        ];
    }
}
