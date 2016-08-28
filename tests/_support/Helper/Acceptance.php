<?php

namespace Helper;

use HelperTrait;
use Codeception\Module;

/**
 * Acceptance Helper
 *
 * Here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
 * @package Helper
 */
class Acceptance extends Module
{
    use HelperTrait;

    public function seeResponseRegexp($regexp, $content)
    {
        $this->assertRegExp($regexp, $content);
    }
}
