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

namespace Phosphorum\Core\Traits;

use Phalcon\Di;
use Phalcon\Platform\Exceptions\DomainException;
use Phosphorum\Core\Environment;
use Phosphorum\Core\TextManager;

/**
 * Phosphorum\Core\Traits\FileSystemTrait
 *
 * @package Phosphorum\Core\Traits
 */
trait FileSystemTrait
{
    /**
     * Resolve the path (make it absolute) and reduce slashes if needed.
     *
     * @param  string $path
     * @param  bool   $appendDirectotySeparator
     *
     * @return string
     *
     * @throws DomainException
     */
    protected function resolveAbsolutePath(string $path, bool $appendDirectotySeparator = true): string
    {
        if (ctype_print($path) == false) {
            throw new DomainException(
                'The path can not have non-printable characters or be empty.'
            );
        }

        $path = rtrim($path, '\\/');

        if ($appendDirectotySeparator == true) {
            $path .= DIRECTORY_SEPARATOR;
        }

        // Looks like it is relative path
        if ($path[0] !== DIRECTORY_SEPARATOR && preg_match('#\A[A-Z]:(?![^/\\\\])#i', $path) == 0) {
            $env = Di::getDefault()->get(Environment::class);
            $path = $env->getPath($path);
        }

        /** @var TextManager $textManager */
        $textManager = Di::getDefault()->get(TextManager::class);

        return $textManager->reduceSlashes($path);
    }
}
