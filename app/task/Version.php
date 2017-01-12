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

namespace Phosphorum\Task;

use Phosphorum\Console\AbstractTask;

/**
 * Phosphorum\Task\Version
 *
 * @package Phosphorum\Task
 */
class Version extends AbstractTask
{
    /**
     * @Doc("Getting the application version")
     */
    public function main()
    {
        $sha = $this->getCommitSha();
        if (!empty($sha)) {
            $sha = ', git commit ' . substr($sha, 0, 7);
        }

        $this->output(
            sprintf(
                '%s version %s%s',
                container('app')->getName(),
                container('app')->getVersion(),
                $sha
            )
        );
    }

    protected function getCommitSha()
    {
        $gitDir = $this->basePath . DIRECTORY_SEPARATOR . '.git';

        if (!file_exists($gitDir) || !$this->isShellCommandExist('git')) {
            return '';
        }

        return implode(' ', $this->runShellCommand('git rev-parse HEAD', false));
    }
}
