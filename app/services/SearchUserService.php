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
*/

namespace Phosphorum\Services;

use Phalcon\Di;
use Phosphorum\Exception\InvalidParameterException;

/**
 * Service add, update, delete and get users data from search engine
 * Phosphorum\Services\SearchUserService
 *
 * <code>
 * $service = new SearchUserService();
 *
 * //To add new user to search index. Return true|false
 * $service->addUserToIndex([
 *     'id' => 1234, //user ID, (require)
 *     'name' => foo bar, //user name, (require)
 *     'login' => fooBar, //user login, (require)
 *     'gravatar_id' => 'e9c56720e4ae1829ee169ca2a25fbf7e', //picture id, (require)
 * ]);
 *
 * //To update data in index. Allowed data will be changed in index. Return true|false
 * $service->updateUserById([
 *     'id' => 1234, //user ID, (require)
 *     'login' => fooBar2, //user login, (not require)
 * ]);
 *
 * //To delete user from index. Return true|false
 * $service->deleteUserById(1234);
 *
 * //To get user data from index by user id. Return array with user data if user exists or empty array
 * $service->getUserDataByUserId(1234);
 *
 * //To get users data from index by part of user name. Must be over 1 symbol.
 * //Return array with users data if users exist or empty array
 * $service->getUserDataByPartOfUserName('f');
 * </code>
 *
 * @package Phosphorum\Services
 */
class SearchUserService
{
    /** @var \Elasticsearch\Client $searchEngine */
    private $searchEngine;

    /** @var array $params */
    private $params = [];

    public function __construct(array $params = [])
    {
        $this->searchEngine = Di::getDefault()->get('elastic');

        $this->setParams($params);
    }

    /**
     * @param array $userParams
     * @return array
     * @throws InvalidParameterException
     */
    public function addUserToIndex(array $userParams)
    {
        if (!$this->isActive()) {
            return false;
        }

        if (!$this->hasArrayKeys(['id', 'name', 'login', 'gravatar_id'], $userParams)) {
            throw new InvalidParameterException("Incorrect passed parameters");
        }

        $extraParam['id'] = (string)$userParams['id'];
        $extraParam['body'] = [
            'id' => (int)$userParams['id'],
            'name' => (string)$userParams['name'],
            'login' => (string)$userParams['login'],
            'gravatar_id' => (string)$userParams['gravatar_id'],
        ];

        $response = $this->searchEngine->index($this->addExtraParam($extraParam));
        if (isset($response['result']) && $response['result'] == 'created') {
            return true;
        }

        return false;
    }

    /**
     * @param array $params
     * @return bool
     * @throws InvalidParameterException
     */
    public function updateUserById(array $params)
    {
        if (!$this->isActive()) {
            return false;
        }
        
        if (!isset($params['id'])) {
            throw new InvalidParameterException('User id parameter is require');
        }

        $user = $this->getUserById($params['id']);
        if (empty($user)) {
            return false;
        }

        foreach ($user['_source'] as $key => $value) {
            if (isset($params[$key])) {
                $user['_source'][$key] = $params[$key];
            }
        }

        $extraParam['body']['doc'] = $user['_source'];
        $extraParam['id'] = $user['_id'];
        $response = $this->searchEngine->update($this->addExtraParam($extraParam));

        if (isset($response['result']) && $response['result'] == 'updated') {
            return true;
        }

        return false;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function deleteUserById($userId)
    {
        if (!$this->isActive()) {
            return false;
        }
        
        $user = $this->getUserById($userId);
        if (empty($user)) {
            return false;
        }

        $extraParam['id'] = $user['_id'];
        $response = $this->searchEngine->delete($this->addExtraParam($extraParam));
        if (isset($response['result']) && $response['result'] == 'deleted') {
            return true;
        }

        return false;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserDataByUserId($userId)
    {
        if (!$this->isActive()) {
            return [];
        }
        
        $user = $this->getUserById($userId);
        if (empty($user)) {
            return [];
        }

        return $user['_source'];
    }

    /**
     * @param string $partOfName
     * @return array
     * @throws InvalidParameterException
     */
    public function getUserDataByPartOfUserName($partOfName)
    {
        if (!$this->isActive()) {
            return [];
        }
        
        if ($partOfName == '') {
            throw new InvalidParameterException("The path of user name can't be empty");
        }

        $extraParam['body']['query']['match_phrase_prefix']['name'] = $partOfName;
        $extraParam['size'] = 8;
        $usersData = $this->getDataFromIndex($this->addExtraParam($extraParam));

        return $this->getUsersData($usersData);
    }

    /**
     * @return bool
     */
    private function isActive()
    {
        if ($this->searchEngine->ping()) {
            return true;
        }
        
        return false;
    }

    /**
     * @param array $params
     * @return void
     */
    private function setParams(array $params)
    {
        $this->params['index'] = getenv('ELASTIC_INDEX');
        $this->params['type'] = getenv('ELASTIC_USER_TYPE');

        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }
    }

    /**
     * @param array $arrayKeys
     * @param array $haystack
     * @return bool
     */
    private function hasArrayKeys(array $arrayKeys, array $haystack)
    {
        foreach ($arrayKeys as $key) {
            if (!array_key_exists($key, $haystack)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $params
     * @return array
     */
    private function addExtraParam(array $params)
    {
        return array_merge($params, $this->params);
    }

    /**
     * @param int $userId
     * @return array
     * @throws InvalidParameterException
     */
    private function getUserById($userId)
    {
        if ($userId < 1) {
            throw new InvalidParameterException("User id mustn't be less than 1");
        }
        $extraParam['id'] = (string)$userId;

        return $this->searchEngine->get($this->addExtraParam($extraParam));
    }

    /**
     * @param array $params
     * @return array
     */
    private function getDataFromIndex(array $params)
    {
        $response = $this->searchEngine->search($params);

        if (isset($response['hits']['hits'])) {
            return $response['hits']['hits'];
        }

        return [];
    }

    /**
     * @var array $dataFromIndex
     * @return array
     */
    private function getUsersData(array $dataFromIndex)
    {
        $data = [];
        foreach ($dataFromIndex as $value) {
            $data[] = $value['_source'];
        }

        return $data;
    }
}
