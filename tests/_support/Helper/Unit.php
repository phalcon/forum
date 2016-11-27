<?php

namespace Helper;

use Mockery;
use Codeception\Module;
use Codeception\TestInterface;

/**
 * Unit Helper
 *
 * Here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
 * @package Helper
 */
class Unit extends Module
{
    /**
     * @var \Codeception\Module\Phalcon
     */
    protected $phalcon = null;

    /**
     * HOOK: used after configuration is loaded
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function _initialize()
    {
        $this->phalcon = $this->getModule('Phalcon');
    }

    /**
     * Executed after each test
     * HOOK: after suite
     *
     * @param TestInterface $test
     */
    public function _after(TestInterface $test)
    {
        Mockery::close();
    }

    /**
     * Get Di from Phalcon module
     *
     * @return \Phalcon\DiInterface
     */
    public function getDi()
    {
        return $this->phalcon->di;
    }

    /**
     * Get Phalcon module
     *
     * @return \Codeception\Module\Phalcon
     */
    public function getPhalcon()
    {
        return $this->phalcon;
    }
}
