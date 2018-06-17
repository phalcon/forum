<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Frontend\ReCaptcha;

use Phalcon\Config;
use Phalcon\Tag;
use ReCaptcha\ReCaptcha as GoogleCaptcha;

/**
 * Phosphorum\Frontend\ReCaptcha\ReCaptchaManager
 *
 * @package Phosphorum\Frontend\ReCaptcha
 */
final class ReCaptchaManager
{
    /** @var GoogleCaptcha */
    protected $captcha;

    /** @var Tag */
    protected $tagManager;

    protected $enabled = false;

    /**
     * ReCaptchaManager constructor.
     *
     * @param Config $config
     * @param Tag    $tagManager
     */
    public function __construct(Config $config, Tag $tagManager)
    {
        $this->tagManager = $tagManager;

        if ($config->offsetExists('secret') && $config->offsetGet('enabled')) {
            $this->enabled = true;
            $this->captcha = new GoogleCaptcha($config->offsetGet('secret'));
        }
    }

    /**
     * Is ReCaptcha enabled?
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Gets ReCaptcha instance if any.
     *
     * @return GoogleCaptcha|null
     */
    public function getCaptcha()
    {
        return $this->captcha;
    }

    /**
     * Get ReCaptcha's JS to include.
     *
     * @return string
     */
    public function getJs(): string
    {
        return $this->tagManager->javascriptInclude('https://www.google.com/recaptcha/api.js', false);
    }
}
