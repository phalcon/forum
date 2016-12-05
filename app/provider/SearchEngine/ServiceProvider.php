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

namespace Phosphorum\Provider\SearchEngine;

use Elasticsearch\Client;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\SearchEngine\ServiceProvider
 *
 * @package Phosphorum\Provider\SearchEngine
 */
class ServiceProvider extends AbstractServiceProvider
{
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = 9200;

    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'elastic';

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
                $config = container('config')->elasticsearch;
                $hosts  = $config->hosts->toArray();

                if (empty($hosts)) {
                    // Fallback
                    $hosts = ['127.0.0.1:9200'];
                }

                return new Client(['hosts' => $hosts]);
            }
        );
    }
}
