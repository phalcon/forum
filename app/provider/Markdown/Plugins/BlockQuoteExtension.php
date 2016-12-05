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
use Ciconia\Renderer\RendererAwareTrait;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Renderer\RendererAwareInterface;

/**
 * Phosphorum\Provider\Markdown\Plugins\BlockQuoteExtension
 *
 * Converts text to <blockquote>
 *
 * Original source code from Markdown.pl
 *
 * Copyright (c) 2004 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 *
 * @author  Kazuyuki Hayashi <hayashi@valnur.net>
 * @package Phosphorum\Provider\Markdown\Plugins
 */
class BlockQuoteExtension implements ExtensionInterface, RendererAwareInterface
{

    use RendererAwareTrait;

    /**
     * @var Markdown
     */
    private $markdown;

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $this->markdown = $markdown;

        $markdown->on('block', [$this, 'processBlockQuote'], 50);
    }

    /**
     * @param Text $text
     */
    public function processBlockQuote(Text $text)
    {
        $text->replace(
            '{
            (?:
            (?:
              ^[ \t]*&gt;[ \t]? # > at the start of a line
                .+\n            # rest of the first line
              (?:.+\n)*         # subsequent consecutive lines
              \n*               # blanks
            )+
            )
            }mx',
            function (Text $bq) {
                $bq->replace('/^[ \t]*&gt;[ \t]?/m', '');
                $bq->replace('/^[ \t]+$/m', '');

                $this->markdown->emit('block', [$bq]);

                $bq->replace('|\s*<pre>.+?</pre>|s', function (Text $pre) {
                    return $pre->replace('/^  /m', '');
                });

                return $this->getRenderer()->renderBlockQuote($bq) . "\n\n";
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'blockquote';
    }
}
