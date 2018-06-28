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

namespace Phosphorum\Core\Domain;

use Phalcon\Di\InjectionAwareInterface;
use Phosphorum\Core\Traits\InjectionAwareTrait;

/**
 * Phosphorum\Core\Domain\RepositoryFactory
 *
 * @package Phosphorum\Core\Domain
 */
class RepositoryFactory implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Create repository by enoty name.
     *
     * @param string $entityName
     *
     * @return RepositoryInterface
     */
    public function createByEntityName(string $entityName): RepositoryInterface
    {
        $className = "\\Phosphorum\\Domain\\Repositories\\{$entityName}Repository";

        if (!class_exists($className)) {
            throw new InvalidRepositoryException("Repository {$className} doesn't exists.");
        }

        return $this->getDI()->get($this->getDI());
    }
}
