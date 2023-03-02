<?php

declare(strict_types=1);

namespace muqsit\thinkit\random;

use muqsit\thinkit\Random;
use function mt_rand;

final class MersenneTwisterRandom implements Random{

	public static function instance() : self{
		static $instance = null;
		return $instance ??= new self();
	}

	private function __construct(){
	}

	public function generateRange(int $min, int $max) : int{
		return mt_rand($min, $max);
	}
}