<?php

use Codeception\Specify;
use Phalcon\Http\Request;
use Codeception\Test\Unit;
use Phosphorum\Provider\Security\Security;

class SecurityTest extends Unit
{
    use Specify;

    /**
     * UnitTester Object
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Executed before each test
     */
    protected function _before()
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('Warning: openssl extension is not loaded');
        }
    }

    /**
     * Tests Security::getPrefixedToken and Security::getPrefixedTokenKey for generating only one token per request
     */
    public function testOnePrefixedTokenPerRequest()
    {
        $this->specify(
            "The prefixed token and prefixed token key do not return one token per request",
            function () {
                $di = $this->setupDI();

                $s = new Security();
                $s->setDI($di);

                $tokenKey = $s->getPrefixedTokenKey('x');
                $token = $s->getPrefixedToken('x');

                expect($tokenKey)->equals($s->getPrefixedTokenKey('x'));
                expect($token)->equals($s->getPrefixedToken('x'));
                expect($token)->equals($s->getPrefixedSessionToken('x'));
            }
        );
    }

    /**
     * Tests Security::checkPrefixedToken method
     */
    public function testCheckPrefixedToken()
    {
        $this->specify(
            'The Security::checkPrefixedToken works incorrectly',
            function () {
                $di = $this->setupDI();

                $s = new Security();
                $s->setDI($di);

                // Random token and token key check
                $tokenKey = $s->getPrefixedTokenKey('y');
                $token = $s->getPrefixedToken('y');

                $_POST = [$tokenKey => $token];

                expect($s->checkPrefixedToken('y', null, null, false))->true();
                expect($s->checkPrefixedToken('y'))->true();
                expect($s->checkPrefixedToken('y'))->false();

                // Destroy token check
                $tokenKey = $s->getPrefixedToken('z');
                $token = $s->getPrefixedToken('z');

                $s->destroyPrefixedToken('z');

                $_POST = [$tokenKey => $token];

                expect($s->checkPrefixedToken('z'))->false();

                // Custom token key check
                $token = $s->getPrefixedToken('abc');

                $_POST = ['custom_key' => $token];

                expect($s->checkPrefixedToken('abc', null, null, false))->false();
                expect($s->checkPrefixedToken('abc', 'other_custom_key', null, false))->false();
                expect($s->checkPrefixedToken('abc', 'custom_key'))->true();

                // Custom token value check
                $token = $s->getPrefixedToken('xyz');

                $_POST = [];

                expect($s->checkPrefixedToken('xyz', null, null, false))->false();
                expect($s->checkPrefixedToken('xyz', 'some_random_key', 'some_random_value', false))->false();
                expect($s->checkPrefixedToken('xyz', 'custom_key', $token))->true();
            }
        );
    }

    /**
     * Set up the environment.
     *
     * @return \Phalcon\DiInterface
     */
    private function setupDI()
    {
        $di = $this->tester->getDi();

        $this->tester->haveServiceInDi('request', function() {
            return new Request();
        }, true);

        return $di;
    }
}
