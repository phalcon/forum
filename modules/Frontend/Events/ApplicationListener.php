<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Frontend\Events;

use Phalcon\Application;
use Phalcon\Events\EventInterface;
use Phalcon\Mvc\User\Component;

/**
 * Phosphorum\Frontend\Events\ApplicationListener
 *
 * @package Phosphorum\Frontend\Events
 */
class ApplicationListener extends Component
{
    /**
     * Default module name.
     *
     * @var string
     */
    private $moduleName;

    /**
     * ApplicationListener constructor.
     *
     * @param string $moduleName
     */
    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * Perform initialization actions on application boot,
     *
     * @param  EventInterface $event
     * @param  Application    $application
     *
     * @return bool
     */
    public function boot(EventInterface $event, Application $application)
    {
        $application->setDefaultModule($this->moduleName);

        return true;
    }
}
