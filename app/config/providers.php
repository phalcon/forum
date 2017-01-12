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

return [
    // Application Service Providers
    Phosphorum\Provider\Config\ServiceProvider::class,
    Phosphorum\Provider\UrlResolver\ServiceProvider::class,
    Phosphorum\Provider\ModelsCache\ServiceProvider::class,
    Phosphorum\Provider\ViewCache\ServiceProvider::class,
    Phosphorum\Provider\Logger\ServiceProvider::class,
    Phosphorum\Provider\Security\ServiceProvider::class,
    Phosphorum\Provider\Session\ServiceProvider::class,
    Phosphorum\Provider\VoltTemplate\ServiceProvider::class,
    Phosphorum\Provider\View\ServiceProvider::class,
    Phosphorum\Provider\Database\ServiceProvider::class,
    Phosphorum\Provider\ModelsManager\ServiceProvider::class,
    Phosphorum\Provider\ModelsMetadata\ServiceProvider::class,
    Phosphorum\Provider\Queue\ServiceProvider::class,
    Phosphorum\Provider\Routing\ServiceProvider::class,
    Phosphorum\Provider\Dispatcher\ServiceProvider::class,
    Phosphorum\Provider\Markdown\ServiceProvider::class,
    Phosphorum\Provider\Notifications\ServiceProvider::class,
    Phosphorum\Provider\Flash\ServiceProvider::class,
    Phosphorum\Provider\SearchEngine\ServiceProvider::class,
    Phosphorum\Provider\Avatar\ServiceProvider::class,
    Phosphorum\Provider\Timezone\ServiceProvider::class,
    Phosphorum\Provider\Breadcrumbs\ServiceProvider::class,
    Phosphorum\Provider\Captcha\ServiceProvider::class,
    Phosphorum\Provider\Annotations\ServiceProvider::class,
    Phosphorum\Provider\FileSystem\ServiceProvider::class,
    Phosphorum\Provider\Email\ServiceProvider::class,
    Phosphorum\Provider\Mailer\ServiceProvider::class,

    // Third Party Providers
    // ...
];
