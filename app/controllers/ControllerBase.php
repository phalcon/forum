<?php

namespace Phosphorum\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phosphorum\Models\Posts;
use Phosphorum\Models\Users;

/**
 * Class ControllerBase
 *
 * @package Phosphorum\Controllers
 *
 * @property \Phalcon\Cache\Backend\Memory viewCache
 * @property \Phalcon\Config config
 * @property \Phosphorum\Utils\Slug slug
 * @property \Phalcon\Avatar\Gravatar gravatar
 */
class ControllerBase extends Controller
{
    public function onConstruct()
    {
        $last_threads = $this
            ->modelsManager
            ->createBuilder()
            ->from(['p' => 'Phosphorum\Models\Posts'])
            ->groupBy("p.id")
            ->join('Phosphorum\Models\Categories', "r.id = p.categories_id", 'r')
            ->join('Phosphorum\Models\Users', "u.id = p.users_id", 'u')
            ->columns([
                'p.title as title_post',
                'p.id as id_post',
                'p.slug as slug_post',
                'r.name as name_category',
                'u.name as name_user'
            ])
            ->orderBy('p.created_at DESC')
            ->limit(3)
            ->getQuery()
            ->execute();

        /** @var Simple $lastMember */
        $lastMember = Users::find(['order' => 'created_at', 'limit' => 1, 'columns' => 'login']);

        $login = null;
        if ($lastMember->valid()) {
            $login = $lastMember->getFirst()->login;
        }

        $this->view->setVars([
            'threads'      => Posts::count(),
            'last_threads' => $last_threads,
            'users'        => Users::count(),
            'users_latest' => $login,
            'actionName'   => $this->dispatcher->getActionName(),
        ]);
    }
}
