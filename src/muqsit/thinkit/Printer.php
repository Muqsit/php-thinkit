<?php

declare(strict_types=1);

namespace muqsit\thinkit;

interface Printer{

	public function print(object $object) : string;
}