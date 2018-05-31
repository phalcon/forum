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

namespace Phosphorum\Markdown;

use ParsedownExtra;

/**
 * Markdown
 *
 * View render
 */
class Markdown extends ParsedownExtra
{
    public function __construct()
    {
        parent::__construct();

        $this->InlineTypes['@'][]= 'UrlMentions';
        $this->inlineMarkerList .= '@';

        $this->InlineTypes['~'][]= 'InsTags';
        $this->inlineMarkerList .= '~';
    }

    /**
     * Extension Added <del> tag to HTML tree
     *
     * Using in editor:
     * Foo ~Some code~ Bar
     *
     * Result in HTML tree:
     * Foo <del>Some code</del> Bar
     *
     * @param array $excerpt
     * @return array
     */
    protected function inlineStrikethrough($Excerpt)
    {
        if (preg_match('/^~{1}([^~].*?)~{1}/', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'del',
                    'text' => $matches[1],
                ],
            ];
        }
    }

    /**
     * Render text. Call in the views
     *
     * @param string $text
     * @return string
     */
    public function render($text)
    {
        return $this->text($text);
    }

    /**
     * Extension makes from '@12345' to `/user/0/12345`
     *
     * @param array $excerpt
     * @return array
     */
    protected function inlineUrlMentions($excerpt)
    {
        $regexp = '/(?:^|[^a-zA-Z0-9.])@([A-Za-z0-9^\-\_]+)/';

        if (preg_match($regexp, $excerpt['context'], $matches, PREG_OFFSET_CAPTURE)) {
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
    }

    /**
     * Extension Added <ins> tag to HTML tree
     *
     * Using in editor:
     * Foo ~~Some code~~ Bar
     *
     * Result in HTML tree:
     * Foo <ins>Some code</ins> Bar
     *
     * @param array $excerpt
     * @return array
     */
    protected function inlineInsTags($excerpt)
    {
        if (preg_match('/^~{2}(.*?)~{2}/', $excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'ins',
                    'text' => $matches[1],
                ],
            ];
        }
    }
}
