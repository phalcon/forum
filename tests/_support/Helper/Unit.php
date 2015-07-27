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
     * Executed after each test
     * HOOK: after suite
     *
     * @param TestCase $test
     */
    public function _after(TestCase $test)
    {
        Mockery::close();
    }
}
