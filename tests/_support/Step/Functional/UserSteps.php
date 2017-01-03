<?php

namespace Step\Functional;

use Step\FakerTrait;
use Codeception\Scenario;
use Faker\Factory as Faker;

class UserSteps extends \FunctionalTester
{
    use FakerTrait;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        $this->faker = Faker::create();
    }
}
