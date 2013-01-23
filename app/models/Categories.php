<?php

namespace Forum\Models;

use Phalcon\Mvc\Model;

class Categories extends Model
{

	public $id;

	public $name;

	public $slug;

	public $number_posts;

}