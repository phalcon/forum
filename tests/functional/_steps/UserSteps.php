<?php
namespace TestGuy;

class UserSteps extends \TestGuy
{
    function amAdmin()
    {
        $I = $this;
		$I->haveInSession('identity', 1);
		$I->haveInSession('identity-name', 'Phalcon');
    }
}