<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

return [
    // Application Service Providers
    Phosphorum\Providers\Config\ServiceProvider::class,
    Phosphorum\Providers\UrlResolver\ServiceProvider::class,
    Phosphorum\Providers\ModelsCache\ServiceProvider::class,
    Phosphorum\Providers\ViewCache\ServiceProvider::class,
    Phosphorum\Providers\Logger\ServiceProvider::class,
    Phosphorum\Providers\Security\ServiceProvider::class,
    Phosphorum\Providers\Session\ServiceProvider::class,
    Phosphorum\Providers\VoltTemplate\ServiceProvider::class,
    Phosphorum\Providers\View\ServiceProvider::class,
    Phosphorum\Providers\Database\ServiceProvider::class,
    Phosphorum\Providers\ModelsManager\ServiceProvider::class,
    Phosphorum\Providers\ModelsMetadata\ServiceProvider::class,
    Phosphorum\Providers\Queue\ServiceProvider::class,
    Phosphorum\Providers\Routing\ServiceProvider::class,
    Phosphorum\Providers\Dispatcher\ServiceProvider::class,
    Phosphorum\Providers\Markdown\ServiceProvider::class,
    Phosphorum\Providers\Notifications\ServiceProvider::class,
    Phosphorum\Providers\Flash\ServiceProvider::class,

    // Third Party Providers
    // ...
];
