<?php

namespace Phosphorum\Markdown;

use Ciconia\Markdown;
use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

/**
 * Class NewLineExtension
 *
 * @package Phosphorum\Markdown
 */
class NewLineExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $markdown->on('inline', [$this, 'processNewLines']);
    }

    public function processNewLines(Text $text)
    {
        $text->replace('/\n/', '<br>');

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'newline';
    }
}
