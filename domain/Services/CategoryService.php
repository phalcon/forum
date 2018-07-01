<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phalcon Platform                                                       |
 +------------------------------------------------------------------------+
 | Copyright (c) 2018 Phalcon Team                                        |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Domain\Services;

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Platform\Domain\AbstractService;
use Phosphorum\Domain\Repositories\CategoryRepository;

/**
 * Phosphorum\Domain\Services\CategoryService
 *
 * @method CategoryRepository getRepository()
 *
 * @package Phosphorum\Domain\Services
 */
class CategoryService extends AbstractService
{
    /**
     * PostTrackingService constructor.
     *
     * @param CategoryRepository $repository
     */
    public function __construct(CategoryRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get ordered categories list.
     *
     * @return ResultsetInterface
     */
    public function getOrderedList(): ResultsetInterface
    {
        return $this->getRepository()->find(['order' => 'numberPosts DESC, name']);
    }
}
