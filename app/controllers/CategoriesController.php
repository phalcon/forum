<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
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

use Phalcon\Mvc\Controller;
use Phosphorum\Models\Categories;
use Phosphorum\Models\Posts;
use Phosphorum\Models\PostsReplies;
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
        $userId = $this->session->get('identity');

        foreach (Categories::find() as $category) {
          if (count(\Phosphorum\Models\Posts::find("categories_id=".$category->id)) > 0) {
                $last_author[$category->id] = $this
                ->modelsManager
                ->createBuilder()
                ->from(array('p' => 'Phosphorum\Models\Posts'))
                ->where('p.categories_id = "'.$category->id.'"')
                ->join('Phosphorum\Models\Users', "u.id = p.users_id", 'u')
                ->columns(array('p.users_id as users_id','u.name as name_user','p.title as post1_title','p.slug as post1_slug','p.id as post1_id'))
                ->orderBy('p.id DESC')
                ->limit(1)
                ->getQuery()
                ->execute();
            } else {
                $last_author[$category->id] = 0;
            }

          //SQL 

            $sql[$category->id] = "SELECT * FROM `posts` JOIN topic_tracking ON topic_tracking.topic_id WHERE concat(posts.id) AND NOT(FIND_IN_SET(posts.id, topic_tracking.topic_id)) AND categories_id = '{$category->id}' AND topic_tracking.user_id = '{$userId}'";
            $not_read[$category->id] = $this->db->query($sql[$category->id]);

        }

          if ($userId !='') {
            $check_topic = new TopicTracking();
            $check_topic->user_id = ''.$this->session->get('identity').'';
            $check_topic->topic_id = '9999999';
            $check_topic->create();
          }

          $this->view->last_author = $last_author;
          $this->view->not_read = $not_read;
          $this->view->logged = $this->session->get('identity');
          $this->view->categories = Categories::find();
    }

}
