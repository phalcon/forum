<?php

namespace Helper;

use Codeception\Module;
use Codeception\TestCase;
use Mockery;

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
     * @var \Codeception\Module\Phalcon1
     */
    protected $phalcon = null;

    /**
     * HOOK: used after configuration is loaded
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function _initialize()
    {
        $this->phalcon = $this->getModule('Phalcon1');
    }

    /**
     * Executed after each test
     * HOOK: after suite
     *
     * @param TestCase $test
     */
    public function _after(TestCase $test)
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
     * @return \Codeception\Module\Phalcon1
     */
    public function getPhalcon()
    {
        return $this->phalcon;
    }
}
