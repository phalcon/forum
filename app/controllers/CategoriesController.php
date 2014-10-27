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
		
		foreach(Categories::find() as $category) {
		if(count(\Phosphorum\Models\Posts::find("categories_id=".$category->id)) > 0) {
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
		}
		
		$this->view->last_author = $last_author;
		$this->view->categories = Categories::find();
    }
	
}
