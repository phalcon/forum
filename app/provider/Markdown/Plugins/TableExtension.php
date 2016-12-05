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
use Ciconia\Common\Tag;
use Ciconia\Common\Text;
use Ciconia\Common\Collection;
use Ciconia\Exception\SyntaxError;
use Ciconia\Renderer\HtmlRenderer;
use Ciconia\Renderer\RendererAwareTrait;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Renderer\RendererAwareInterface;

/**
 * Phosphorum\Provider\Markdown\Plugins\TableExtension
 *
 * @author  Kazuyuki Hayashi <hayashi@valnur.net>
 * @package Phosphorum\Provider\Markdown\Plugins
 */
class TableExtension implements ExtensionInterface, RendererAwareInterface
{

    use RendererAwareTrait;

    /**
     * @var Markdown
     */
    private $markdown;

    /**
     * @var string
     */
    private $hash;

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $this->markdown = $markdown;
        $this->hash = '{gfm:table:escape(' . md5('|') . ')}';

        if ($this->getRenderer() instanceof HtmlRenderer) {
            // Output format depends on HTML for now
            $markdown->on('block', [$this, 'processTable']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'table'; // Not gfmTable
    }

    /**
     * Gfm tables
     *
     * @param Text  $text
     * @param array $options
     */
    public function processTable(Text $text, array $options = [])
    {
        $lessThanTab = $options['tabWidth'] - 1;

        $text->replace(
            '/
            (?:\n\n|\A)
            (?:[ ]{0,' . $lessThanTab . '}      #  table header
                (?:\|?)                         #  optional outer pipe
                ([^\n]*?\|[^\n]*?)              #1 table header
                (?:\|?)                         #  optional outer pipe
            )\n
            (?:[ ]{0,' . $lessThanTab . '}      #  second line
                (?:\|?)                         #  optional outer pipe
                ([-:\| ]+?\|[-:\| ]+?)          #2 dashes and pipes
                (?:\|?)                         #  optional outer pipe
            )\n
            (.*?)\n{2,}                         #3 table body
            /smx',
            function (Text $w, Text $header, Text $rule, Text $body) use ($options) {
                // Escape pipe to hash, so you can include pipe in cells by escaping it like this: `\\|`
                $this->escapePipes($header);
                $this->escapePipes($rule);
                $this->escapePipes($body);

                try {
                    $baseTags    = $this->createBaseTags($rule->split('/\|/'));
                    $headerCells = $this->parseHeader($header, $baseTags);
                    $bodyRows    = $this->parseBody($body, $baseTags);
                } catch (SyntaxError $e) {
                    if ($options['strict']) {
                        throw $e;
                    }

                    return $w;
                }

                $html = $this->createView($headerCells, $bodyRows);
                $this->unescapePipes($html);

                return "\n\n" . $html . "\n\n";
            }
        );
    }

    /**
     * @param Collection $headerCells
     * @param Collection $bodyRows
     *
     * @return Text
     */
    protected function createView(Collection $headerCells, Collection $bodyRows)
    {
        $tHeadRow = new Tag('tr');
        $tHeadRow->setText("\n" . $headerCells->join("\n") . "\n");

        $tHead = new Tag('thead');
        $tHead->setText("\n" . $tHeadRow . "\n");

        $tBody = new Tag('tbody');

        $bodyRows->apply(function (Collection $row) use (&$options) {
            $tr = new Tag('tr');
            $tr->setText("\n" . $row->join("\n") . "\n");

            return $tr;
        });

        $tBody->setText("\n" .$bodyRows->join("\n") . "\n");

        $table = new Tag('table');
        $table->setAttributes(['class' => 'table']);
        $table->setText("\n" . $tHead . "\n" . $tBody . "\n");

        return new Text($table->render());
    }

    /**
     * @param Collection $rules
     *
     * @return Collection|\Ciconia\Common\Tag[]
     */
    protected function createBaseTags(Collection $rules)
    {
        /* @var Collection|Tag[] $baseTags */
        $baseTags = new Collection();

        $rules->each(function (Text $cell) use (&$baseTags) {
            $cell->trim();
            $tag = new Tag('td');

            if ($cell->match('/^-.*:$/')) {
                $tag->setAttribute('align', 'right');
            } elseif ($cell->match('/^:.*:$/')) {
                $tag->setAttribute('align', 'center');
            }

            $baseTags->add($tag);
        });

        return $baseTags;
    }

    /**
     * @param Text       $header
     * @param Collection $baseTags
     *
     * @throws \Ciconia\Exception\SyntaxError
     *
     * @return Collection
     */
    protected function parseHeader(Text $header, Collection $baseTags)
    {
        $cells = new Collection();

        try {
            $header->split('/\|/')->each(function (Text $cell, $index) use ($baseTags, &$cells) {
                /* @var Tag $tag */
                $tag = clone $baseTags->get($index);
                $tag->setName('th');
                $this->markdown->emit('inline', [$cell]);
                $tag->setText($cell->trim());

                $cells->add($tag);
            });
        } catch (\OutOfBoundsException $e) {
            throw new SyntaxError('Too much cells on table header.', $this, $header, $this->markdown, $e);
        }

        if ($baseTags->count() != $cells->count()) {
            throw new SyntaxError('Unexpected number of table cells in header.', $this, $header, $this->markdown);
        }

        return $cells;
    }

    /**
     * @param Text       $body
     * @param Collection $baseTags
     *
     * @return Collection
     */
    protected function parseBody(Text $body, Collection $baseTags)
    {
        $rows = new Collection();

        $body->split('/\n/')->each(function (Text $row, $index) use ($baseTags, &$rows) {
            $row->trim()->trim('|');

            $cells = new Collection();

            try {
                $row->split('/\|/')->each(function (Text $cell, $index) use (&$baseTags, &$cells) {
                    /* @var Tag $tag */
                    $tag = clone $baseTags->get($index);
                    $this->markdown->emit('inline', [$cell]);
                    $tag->setText($cell->trim());

                    $cells->add($tag);
                });
            } catch (\OutOfBoundsException $e) {
                throw new SyntaxError(
                    sprintf('Too much cells on table body (row #%d).', $index),
                    $this,
                    $row,
                    $this->markdown,
                    $e
                );
            }

            if ($baseTags->count() != $cells->count()) {
                throw new SyntaxError('Unexpected number of table cells in body.', $this, $row, $this->markdown);
            }

            $rows->add($cells);
        });

        return $rows;
    }

    /**
     * @param Text $text
     */
    protected function escapePipes(Text $text)
    {
        $text->replaceString('\\|', $this->hash);
    }

    /**
     * @param Text $text
     */
    protected function unescapePipes(Text $text)
    {
        $text->replaceString($this->hash, '|');
    }
}
