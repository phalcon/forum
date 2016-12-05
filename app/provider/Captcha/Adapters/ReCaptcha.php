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

namespace Phosphorum\Provider\Captcha\Adapters;

use Phalcon\Tag;
use Phalcon\Config;
use ReCaptcha\ReCaptcha as GoogleCaptcha;

/**
 * Phosphorum\Provider\Captcha\Adapters\ReCaptcha
 *
 * @package Phosphorum\Provider\Captcha\Adapters
 */
class ReCaptcha
{
    /**
     * @var GoogleCaptcha
     */
    protected $captcha;

    protected $enabled = false;

    public function __construct()
    {
        /** @var Config $config */
        $config = container('config')->reCaptcha;
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
