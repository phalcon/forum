<?php

namespace Phosphorum\Markdown;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Renderer\RendererAwareInterface;
use Ciconia\Renderer\RendererAwareTrait;
use Ciconia\Markdown;

/**
 * Converts text to <blockquote>
 *
 * Original source code from Markdown.pl
 *
 * > Copyright (c) 2004 John Gruber
 * > <http://daringfireball.net/projects/markdown/>
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
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

        $markdown->on('block', array($this, 'processBlockQuote'), 50);
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
              ^[ \t]*&gt;[ \t]? # \'>\' at the start of a line
                .+\n         # rest of the first line
              (?:.+\n)*      # subsequent consecutive lines
              \n*            # blanks
            )+
            )
            }
            mx',
            function (Text $bq) {
                $bq->replace('/^[ \t]*&gt;[ \t]?/m', '');
                $bq->replace('/^[ \t]+$/m', '');

                $this->markdown->emit('block', array($bq));

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
