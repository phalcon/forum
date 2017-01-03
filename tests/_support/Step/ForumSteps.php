<?php

namespace Step;

use Codeception\Scenario;
use Faker\Factory as Faker;

class ForumSteps extends \FunctionalTester
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
