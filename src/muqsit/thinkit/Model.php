<?php

declare(strict_types=1);

namespace muqsit\thinkit;

interface Model{

	public function train(Matrix $input, Matrix $output) : void;

	public function predict(Matrix $input) : Matrix;
}