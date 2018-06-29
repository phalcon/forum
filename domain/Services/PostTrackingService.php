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

namespace Phosphorum\Domain\Services;

use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Row;
use Phalcon\Platform\Domain\AbstractService;
use Phosphorum\Domain\Repositories\PostTrackingRepository;

/**
 * Phosphorum\Domain\Services\PostTrackingService
 *
 * @method PostTrackingRepository getRepository()
 *
 * @package Phosphorum\Domain\Services
 */
class PostTrackingService extends AbstractService
{
    /**
     * Gets the message IDs that the user has already read.
     *
     * @param  int $userId
     *
     * @return int[]
     */
    public function getReadPostsIds(int $userId): array
    {
        $posts = $this
            ->getRepository()
            ->getEntity()
            ->findFirst([
                'userId = :user_id:',
                'bind'      => ['user_id' => $userId],
                'bindTypes' => ['user_id' => Column::BIND_PARAM_INT],
                'columns'   => ['postId'],
            ]);

        $postIds = [];

        if ($posts instanceof Row == true) {
            $postIds = explode(',', $posts->offsetGet('postId'));
        }

        return array_map(function ($value) {
            return (int) $value;
        }, array_filter($postIds));
    }
}
