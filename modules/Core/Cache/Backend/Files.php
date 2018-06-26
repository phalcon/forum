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

namespace Phosphorum\Core\Cache\Backend;

use Phalcon\Cache\Backend\File;
use Phalcon\Cache\FrontendInterface;
use Phosphorum\Core\Exceptions\DomainException;
use Phosphorum\Core\Exceptions\InvalidArgumentException;
use Phosphorum\Core\Traits\FileSystemTrait;

/**
 * Phosphorum\Core\Cache\Backend\Files
 *
 * @package Phosphorum\Core\Cache\Backend
 */
class Files extends File
{
    use FileSystemTrait;

    /**
     * Files constructor.
     *
     * @param  FrontendInterface $frontend
     * @param  array             $options
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function __construct(FrontendInterface $frontend, array $options)
    {
        if (isset($options['cacheDir'])) {
            $options['cacheDir'] = $this->resolveAbsolutePath($options['cacheDir']);
        }

        parent::__construct($frontend, $options);
    }
}
