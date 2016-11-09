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

namespace Phosphorum;

use Phalcon\Config;
use Phalcon\Di\Injectable;
use Phalcon\Tag;
use ReCaptcha\ReCaptcha as GoogleCaptcha;

/**
 * Class ReCaptcha
 * @package Phosphorum
 *
 * @property \Phalcon\Config config
 */
class ReCaptcha extends Injectable
{
    /**
     * @var GoogleCaptcha
     */
    protected $captcha;

    protected $enabled = false;

    public function __construct()
    {
        /** @var Config $config */
        $config = $this->config->get('reCaptcha');
        if ($config instanceof Config && $config->offsetGet('secret') && $config->offsetGet('siteKey')) {
            $this->enabled = true;
            $this->captcha = new GoogleCaptcha($config->offsetGet('secret'));
        }
    }

    public function isEnabled()
    {
        return (bool) $this->enabled;
    }

    public function getCaptcha()
    {
        return $this->captcha;
    }

    public function getJs()
    {
        return Tag::javascriptInclude('https://www.google.com/recaptcha/api.js', false);
    }
}
