<?php
namespace TestGuy;

class UserSteps extends \TestGuy
{
    function amAdmin()
    {
        $I = $this;
        $id = $I->haveRecord('Phosphorum\Models\Users', ['name' => 'Phalcon']);
		$I->haveInSession('identity', $id);
		$I->haveInSession('identity-name', 'Phalcon');
		return $id;
    }

    function haveCategory($attrs)
	{
		return $this->haveRecord('Phosphorum\Models\Categories', $attrs);		
	}   

	function havePost($attrs) 
	{
		return $this->haveRecord('Phosphorum\Models\Posts', $attrs);		
	}

}