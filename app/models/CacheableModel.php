<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;
use Phalcon\DI;

class CacheableModel extends Model
{

	/**
	 * Caches models data in memory
	 *
	 * @param array $parameters
	 */
	public static function findFirst($parameters=null)
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
			$key = preg_replace('/[^0-9A-Za-z]/', '-', get_called_class(). '-' . $key);
			$parameters['cache'] = array('key' => $key);
		}
		return parent::findFirst($parameters);
	}

	/**
	 * Allows to use the model as a resultset's row
	 *
	 * @param boolean $value
	 */
	public function setIsFresh($value)
	{
		return $this;
	}

	/**
	 * Allows to use the model as a resultset's row
	 *
	 * @param boolean $value
	 */
	public function getFirst()
	{
		return $this;
	}

}