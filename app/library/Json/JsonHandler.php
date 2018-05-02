<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>             |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Json;

/**
 * Class JsonHandler
 * Phosphorum\Json\JsonHandler
 *
 * @package Phosphorum\Json
 */
class JsonHandler
{
    /**
     * @return array
     */
    public function jsonToArray($json): array
    {
        return json_decode($json, true);
    }

    public function updateJson($json, array $recievedData)
    {
        $array = $this->jsonToArray($json);

        foreach ($recievedData as $name => $data) {
            if (isset($array[$name])) {
                $array[$name]['value'] = $data;
            }
        }

        return json_encode($array);
    }
}
