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

namespace Phosphorum\Domain\Repositories;

use Phalcon\Db\Column;
use Phalcon\Platform\Domain\AbstractRepository;
use Phosphorum\Domain\Entities\PostTrackingEntity;

/**
 * Phosphorum\Domain\Repositories\PostTrackingRepository
 *
 * @method PostTrackingEntity getEntity()
 *
 * @package Phosphorum\Domain\Repositories
 */
class PostTrackingRepository extends AbstractRepository
{
    /**
     * PostTrackingRepository constructor.
     *
     * @param PostTrackingEntity $entity
     */
    public function __construct(PostTrackingEntity $entity)
    {
        parent::__construct($entity);
    }

    /**
     * Gets the message IDs that the user has already read.
     *
     * @param  int $userId
     *
     * @return int[]
     */
    public function getReadPostsIds(int $userId): array
    {
        $posts = $this->getEntity()
            ->findFirst([
                'userId = :user_id:',
                'bind'      => ['user_id' => $userId],
                'bindTypes' => ['user_id' => Column::BIND_PARAM_INT],
                'columns'   => ['postId'],
            ]);

        $postIds = explode(',', $posts ? $posts->offsetGet('postId') : '');

        return array_map(function ($value) {
            return (int) $value;
        }, array_filter($postIds));
    }
}
