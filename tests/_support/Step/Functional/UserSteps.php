<?php

namespace Step\Functional;

class UserSteps extends \FunctionalTester
{
    public function amRegularUser()
    {
        $I = $this;
        $id = $I->haveRecord('Phosphorum\Models\Users', ['name' => 'Regular User', 'login' => 'iregular']);
        $I->haveInSession('identity', $id);
        $I->haveInSession('identity-name', 'Regular User');
        return $id;
    }

    public function amAdmin()
    {
        $I = $this;
        $id = $I->haveRecord('Phosphorum\Models\Users', ['name' => 'Phalcon', 'login' => 'phalcon']);
        $I->haveInSession('identity', $id);
        $I->haveInSession('identity-name', 'Phalcon');
        return $id;
    }

    public function haveCategory($attrs)
    {
        return $this->haveRecord('Phosphorum\Models\Categories', $attrs);
    }

    public function havePost($attrs)
    {
        return $this->haveRecord('Phosphorum\Models\Posts', $attrs);
    }

    public function haveReply($attrs)
    {
        return $this->haveRecord('Phosphorum\Models\PostsReplies', $attrs);
    }
}
