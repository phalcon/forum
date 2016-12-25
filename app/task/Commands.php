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

use Phosphorum\Console\TaskFinder;
use Phosphorum\Console\AbstractTask;

/**
 * Phosphorum\Task\Commands
 *
 * @package Phosphorum\Task
 */
class Commands extends AbstractTask
{
    /**
     * @Doc("Getting list of the console tasks")
     */
    public function main()
    {
        $finder = new TaskFinder(app_path('task'));
        $list   = $finder->scan();

        $this->output('');
        $this->output(sprintf('%s %s', container('app')->getName(), container('app')->getVersion()));
        $this->output('');

        $system = [];

        foreach ($list as $commands) {
            foreach ($commands as $command) {
                $name = $command['command'];
                if (!empty($command['name'])) {
                    $name .= ":{$command['name']}";
                }

                if (in_array($name, ['commands', 'help', 'version'])) {
                    $system[] = [
                        'name'        => $name,
                        'description' => $command['description'],
                    ];

                    continue;
                }

                $this->output(sprintf('% 22s         %s', $name, $command['description']));
            }
        }

        $this->output('');

        foreach ($system as $command) {
            $this->output(sprintf('% 22s         %s', $command['name'], $command['description']));
        }

        $this->output('');
    }
}
