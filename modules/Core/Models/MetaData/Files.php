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

namespace Phosphorum\Core\Models\MetaData;

use Phalcon\Di;
use Phalcon\Mvc\Model\MetaData\Files as BaseAdapter;
use Phosphorum\Core\Environment;
use Phosphorum\Core\Exceptions\DomainException;
use Phosphorum\Core\Exceptions\InvalidArgumentException;

/**
 * Phosphorum\Core\Models\MetaData\Files
 *
 * @package Phosphorum\Core\Models\MetaData
 */
class Files extends BaseAdapter
{
    /**
     * Files constructor.
     *
     * @param array $options
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function __construct(array $options = [])
    {
        if (isset($options['metaDataDir'])) {
            $options = $this->resolveMetaDataDir($options);
        }

        parent::__construct($options);
    }

    /**
     * Resolve MetaData path (make it absolute).
     *
     * @param  array $options
     *
     * @return array
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    private function resolveMetaDataDir(array $options): array
    {
        $path = $options['metaDataDir'];

        if (is_string($path) == false) {
            throw new InvalidArgumentException(
                sprintf(
                    'The metaDataDir parameter must be a string, got %s.',
                    gettype($path)
                )
            );
        }

        if (ctype_print($path) == false) {
            throw new DomainException(
                'The metaDataDir can not have non-printable characters or be empty.'
            );
        }

        $options['metaDataDir'] = rtrim($options['metaDataDir'], '\\/') . DIRECTORY_SEPARATOR;

        // Looks like it is relative path
        if ($path[0] !== DIRECTORY_SEPARATOR && preg_match('#\A[A-Z]:(?![^/\\\\])#i', $path) > 0) {
            $env = Di::getDefault()->get(Environment::class);
            $options['metaDataDir'] = $env->getPath($path);
        }

        return $options;
    }
}
