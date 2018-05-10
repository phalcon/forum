<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Model;

use Phalcon\Mvc\Model;

/**
 * Class CacheableModel
 *
 * @package Phosphorum\Model
 */
class CacheableModel extends Model
{
    /**
     * Caches models data in memory
     *
     * @param mixed $parameters
     * @return $this
     */
    public static function findFirst($parameters = null)
    {
        // Create an unique key based on the parameters
        if ($key = self::createKey($parameters)) {
            $parameters['cache'] = ['key' => $key];
        }

        return parent::findFirst($parameters);
    }

    /**
     * Allows to use the model as a resultset's row
     *
     * @param mixed $value
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

    /**
     * Returns a string key based on the query parameters
     *
     * @param mixed $parameters
     * @return null|string
     */
    protected static function createKey($parameters)
    {
        if (!is_array($parameters) || !$parameters['bind']) {
            return null;
        }

        $key = null;

        if (isset($parameters[0])) {
            $key = $parameters[0] . '-' . join('-', $parameters['bind']);
        } elseif (isset($parameters['conditions'])) {
            $key = $parameters['conditions'] . '-' . join('-', $parameters['bind']);
        } else {
            return null;
        }

        return strtolower(
            preg_replace('#[^0-9A-Za-z]#', '-', substr(get_called_class(), 18) . '-' . $key)
        );
    }
}
