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

namespace Phosphorum\Core\Mvc\Model\MetaData;

use Phalcon\Mvc\Model\MetaData\Files as BaseAdapter;
use Phosphorum\Core\Exceptions\DomainException;
use Phosphorum\Core\Exceptions\InvalidArgumentException;
use Phosphorum\Core\Traits\FileSystemTrait;

/**
 * Phosphorum\Core\Mvc\Model\MetaData\Files
 *
 * @package Phosphorum\Core\Mvc\Model\MetaData
 */
class Files extends BaseAdapter
{
    use FileSystemTrait;

    /**
     * Files constructor.
     *
     * @param  array $options
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function __construct(array $options = [])
    {
        if (isset($options['metaDataDir'])) {
            $options['metaDataDir'] = $this->resolveAbsolutePath($options['metaDataDir']);
        }

        parent::__construct($options);
    }
}
