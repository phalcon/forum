<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Controllers;

use Phosphorum\Models\Categories;
use Phosphorum\Models\Posts;
use Phosphorum\Models\TopicTracking;

/**
 * Class IndexController
 *
 * @package Phosphorum\Controllers
 */
class CategoriesController extends ControllerBase
{
    /**
     * @return \Phalcon\Http\ResponseInterface
     */
    public function indexAction()
    {
        $this->tag->setTitle('Forum');

        $userId           = $this->session->get('identity');
        $categories       = Categories::find();
        $lastAuthor       = [];
        $notRead          = [];
        $postsPerCategory = [];

        foreach ($categories as $category) {
            /** @var \Phalcon\Mvc\Model\Resultset\Simple $posts */
            $posts = Posts::find("categories_id=".$category->id);
            $postsPerCategory[$category->id] = $posts->count();
            if ($posts->count()) {
                $lastAuthor[$category->id] = $this
                    ->modelsManager
                    ->createBuilder()
                    ->from(['p' => 'Phosphorum\Models\Posts'])
                    ->where('p.categories_id = "'.$category->id.'"')
                    ->join('Phosphorum\Models\Users', "u.id = p.users_id", 'u')
                    ->columns(['p.users_id as users_id','u.name as name_user','p.title as post1_title','p.slug as post1_slug','p.id as post1_id'])
                    ->orderBy('p.id DESC')
                    ->limit(1)
                    ->getQuery()
                    ->execute();
            } else {
                $postsPerCategory[$category->id] = 0;
                $lastAuthor[$category->id] = 0;
            }

            // SQL
            $sql[$category->id] = "
                SELECT *
                FROM `posts` `p`
                JOIN `topic_tracking` `tt` ON `tt`.`topic_id`
                WHERE CONCAT(`p`.`id`)
                  AND NOT(FIND_IN_SET(`p`.`id`, `tt`.`topic_id`))
                  AND `p`.`categories_id` = '{$category->id}' AND `tt`.`user_id` = '{$userId}';
            ";
            $notRead[$category->id] = $this->db->query($sql[$category->id]);
        }

        if ($userId) {
            $check_topic = new TopicTracking();
            $check_topic->user_id = ''.$userId.'';
            $check_topic->topic_id = '9999999';
            $check_topic->create();
        }

        $this->view->setVars(
            [
                'last_author'        => $lastAuthor,
                'not_read'           => $notRead,
                'logged'             => $userId,
                'categories'         => $categories,
                'posts_per_category' => $postsPerCategory
            ]
        );
    }
}
