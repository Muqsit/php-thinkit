<?php

declare(strict_types=1);

namespace muqsit\thinkit;

interface Random{

	/**
	 * Returns a (pseudo-)random value between given min
	 * and max values (inclusive).
	 *
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public function generateRange(int $min, int $max) : int;
}