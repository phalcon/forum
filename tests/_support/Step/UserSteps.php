<?php

namespace Step;

use HelperTrait;
use Codeception\Scenario;
use Faker\Factory as Faker;

class UserSteps extends \FunctionalTester
{
    use HelperTrait;

    protected $faker;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        $this->faker = Faker::create();
    }
}
