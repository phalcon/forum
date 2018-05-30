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

namespace Phosphorum\Task;

use Phosphorum\System\Platform;
use Phosphorum\Console\AbstractTask;
use Phosphorum\Console\OptionParser;

/**
 * Phosphorum\Task\Install
 *
 * @package Phosphorum\Task
 */
class Install extends AbstractTask
{
    /** @var array */
    protected $pathsList = [
        'assets',
        'logs',
        'pids',
        'annotationsCache',
        'configCache',
        'dataCache',
        'metaDataCache',
        'viewsCache',
        'voltCache',
    ];

    /** @var array */
    protected $data = [
        'realPaths' => [],
        'parsedCommand' => [],
        'userData' => [
            'defaultName' => 'www-data',
        ],
    ];

    /**
     * @Doc("Checking application for minimum requirements")
     */
    public function check()
    {
        $this->outputBackgroundBlue('Start Checking...');
        $this->setData();

        /** Checking existence of folders */
        $this->outputBlue('Checking all folders are exist');
        $this->checkFileSystemElementExistence();
        $this->outputMessage('');

        /** Checking writing permission */
        $this->outputBlue('Checking permission for writing in folders');
        $this->checkFileSystemElementPermission();
        $this->outputMessage('');

        $this->outputBackgroundBlue('Done');
    }

    /**
     * @return void
     */
    protected function setData()
    {
        $this->setPaths();
        $this->setCommand();
    }

    /**
     * @return void
     */
    protected function setPaths()
    {
        /**@var \Phalcon\Registry*/
        $registry = $this->getDI()->get('registry');

        foreach ($this->pathsList as $value) {
            if (property_exists($registry->offsetGet('paths'), $value)) {
                $this->data['realPaths'][] = $registry->offsetGet('paths')->$value;
                continue;
            }

            $this->outputError("Registry container don't have property '{$value}'");
        }
    }

    /**
     * @return void
     */
    protected function setCommand()
    {
        $this->data['parsedCommand'] =  OptionParser::parse($_SERVER['argv']);
    }

    /**
     * Checking filesystem's elements existence
     * @return void
     */
    protected function checkFileSystemElementExistence()
    {
        if (empty($this->data['realPaths'])) {
            $this->outputError("No one folder hasn't been defined");
            return;
        }

        $error = 0;
        foreach ($this->data['realPaths'] as $key => $path) {
            if (!file_exists($path)) {
                $this->outputError("Folder '{$path}' can't be found.");
                unset($this->data['realPaths'][$key]);
                $error++;
            }
        }

        if (!$error) {
            $this->outputInfo("All directories are exist!");
        }
    }

    /**
     * Checking filesystem's elements permission for writing
     * @return void
     */
    protected function checkFileSystemElementPermission()
    {
        if ((new Platform())->isWindows()) {
            $this->outputMessage("Checking writing permission can't be executed on windows");
            return;
        }

        if (!$this->setUserInfo()) {
            return;
        }

        $this->checkPermissions();
    }

    /**
     * @return bool
     */
    public function setUserInfo()
    {
        $userName = !empty($this->data['parsedCommand']['name']) ?
            $this->data['parsedCommand']['name'] : $this->data['userData']['defaultName'];

        if (!function_exists('posix_getpwnam')) {
            $this->outputMessage("POSIX extension hasn't been found");
            return false;
        }

        if (!$this->data['userData'] = posix_getpwnam($userName)) {
            $this->outputMessage("User '{$userName}' hasn't been defined yet");
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function checkPermissions()
    {
        $error = 0;
        foreach ($this->data['realPaths'] as $path) {
            if (!$stat = stat($path)) {
                $this->outputMessage("Information about path '{$path}' can't be got");
                continue;
            }
            $permission = fileperms($path);

            /** if user is folder owner */
            if ($stat['uid'] == $this->data['userData']['uid'] && !$this->assetsWritiblePath($path, $permission, -3)) {
                $error++;
                continue;
            }

            /** if user's group id and directory's group id are equal */
            if (!$groupInfo = $this->getGroupInfoById($stat['gid'])) {
                $error++;
                $this->outputMessage("Group '{$stat['gid']}' hasn't been defined yet");
                continue;
            }
            if (in_array($this->data['userData']['name'], $groupInfo['members'])
                && !$this->assetsWritiblePath($path, $permission, -2)) {
                $error++;
                continue;
            }

            /** for other users */
            if (!$this->assetsWritiblePath($path, $permission, -1)) {
                $error++;
            }
        }

        if (!$error) {
            $this->outputInfo("All directories are writible!");
        }
    }

    /**
     * @param string $path
     * @param int $permission
     * @param int $start
     * @return bool
     */
    protected function assetsWritiblePath($path, $permission, $start)
    {
        if (substr(sprintf('%o', $permission), $start, 1) == 7) {
            return true;
        }

        $this->outputError("Directory '{$path}' isn't writable for user '{$this->data['userData']['name']}'");
        return false;
    }

    /**
     * @param int $groupId
     * @return array | bool
     */
    protected function getGroupInfoById($groupId)
    {
        if (!function_exists('posix_getgrgid')) {
            $this->outputMessage("POSIX extension hasn't been found");
            return false;
        }

        if ($groupInfo = posix_getgrgid($groupId)) {
            return $groupInfo;
        }

        return false;
    }
}
