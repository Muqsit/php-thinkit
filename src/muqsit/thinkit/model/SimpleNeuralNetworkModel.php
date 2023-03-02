<?php

declare(strict_types=1);

namespace muqsit\thinkit\model;

use muqsit\thinkit\Matrix;
use muqsit\thinkit\Model;
use muqsit\thinkit\Random;

final class SimpleNeuralNetworkModel implements Model{

	/**
	 * @param positive-int $iterations
	 * @param Random|null $random
	 * @return self
	 */
	public static function create(int $iterations, ?Random $random = null) : self{
		$weights = Matrix::random(3, 1, $random)->apply(fn($x) => 2 * $x - 1);
		return new self($iterations, $weights);
	}

	/**
	 * @param positive-int $iterations
	 */
	public function __construct(
		/** @readonly */ public int $iterations,
		private Matrix $synaptic_weights
	){}

	public function getSynapticWeights() : Matrix{
		return clone $this->synaptic_weights;
	}

	private function sigmoid(Matrix $x) : Matrix{
		// sigmoid function
		return $x->apply(fn($n) => 1 / (1 + exp(-$n)));
	}

	private function sigmoid_derivative(Matrix $x) : Matrix{
		// create the curve "s"
		return $x->apply(fn($n) => $n * (1 - $n));
	}

	public function train(Matrix $input, Matrix $output) : void{
		// train the NN (adjust the weight and synaptic values)
		for($iteration = 0; $iteration < $this->iterations; $iteration++){
			// pass the process of cycle / loop in the form of neural network
			$result_output = $this->predict($input);

			// calculate the error
			$error = $output->subtract($result_output);

			$adjustment = $input->transpose()->dot($error->multiply($this->sigmoid_derivative($result_output)));

			// adjust the weights
			$this->synaptic_weights = $this->synaptic_weights->add($adjustment);
		}
	}

	public function predict(Matrix $input) : Matrix{
		return $this->sigmoid($input->dot($this->synaptic_weights));
	}
}