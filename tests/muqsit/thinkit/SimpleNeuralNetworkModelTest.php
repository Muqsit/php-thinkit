<?php

declare(strict_types=1);

namespace muqsit\thinkit;

use muqsit\thinkit\model\SimpleNeuralNetworkModel;
use PHPUnit\Framework\TestCase;

final class SimpleNeuralNetworkModelTest extends TestCase{

	public function testTrainingAndPrediction() : void{
		$training_input = Matrix::create([
			[0, 0, 1],
			[1, 1, 1],
			[1, 0, 1],
			[0, 0, 1]
		]);
		$training_output = Matrix::create([[0, 1, 1, 0]])->transpose();

		$model = new SimpleNeuralNetworkModel(iterations: 10_000, synaptic_weights: Matrix::create([[0.14869082, -0.92784140, 0.71774172]])->transpose());

		$model->train(input: $training_input, output: $training_output);
		$this->assertObjectEquals(Matrix::create([[9.33674624, 1.36107454, -4.7245078]])->transpose(), $model->getSynapticWeights()->round(8));

		$prediction = $model->predict(input: Matrix::create([[1, 0, 0]]));
		$this->assertObjectEquals(Matrix::create([[0.99991188]]), $prediction->round(8));
	}
}