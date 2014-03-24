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

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;
use Phalcon\DI;

/**
 * Class CacheableModel
 *
 * @package Phosphorum\Models
 */
class CacheableModel extends Model
{

    /**
     * Caches models data in memory
     *
     * @param null $parameters
     *
     * @return Model
     */
    public static function findFirst($parameters = null)
    {
        $key = null;
        if (isset($parameters[0]) && isset($parameters['bind'])) {
            $key = $parameters[0] . '-' . join('-', $parameters['bind']);
        } else {
            if (isset($parameters['conditions']) && isset($parameters['bind'])) {
                $key = $parameters['conditions'] . '-' . join('-', $parameters['bind']);
            }
        }
        if ($key) {
            $key                 = preg_replace('/[^0-9A-Za-z]/', '-', get_called_class() . '-' . $key);
            $parameters['cache'] = array('key' => $key);
        }
        return parent::findFirst($parameters);
    }

    /**
     * Allows to use the model as a resultset's row
     *
     * @param $value
     *
     * @return $this
     */
    public function setIsFresh($value)
    {
        return $this;
    }

    /**
     * Allows to use the model as a resultset's row
     *
     * @return $this
     */
    public function getFirst()
    {
        return $this;
    }
}
