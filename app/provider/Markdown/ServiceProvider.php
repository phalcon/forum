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

namespace Phosphorum\Provider\Markdown;

use Ciconia\Ciconia;
use Phosphorum\Provider\AbstractServiceProvider;
use Ciconia\Extension\Gfm\FencedCodeBlockExtension;

/**
 * Phosphorum\Provider\Markdown\ServiceProvider
 *
 * @package Phosphorum\Provider\Markdown
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'markdown';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $ciconia = new Ciconia();

                $ciconia->addExtension(new Plugins\UnderscoredUrlsExtension());
                $ciconia->addExtension(new Plugins\TableExtension());
                $ciconia->addExtension(new Plugins\MentionExtension());
                $ciconia->addExtension(new Plugins\BlockQuoteExtension());
                $ciconia->addExtension(new Plugins\UrlAutoLinkExtension());
                $ciconia->addExtension(new FencedCodeBlockExtension());

                return $ciconia;
            }
        );
    }
}
