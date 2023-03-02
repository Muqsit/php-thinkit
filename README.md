# php-thinkit
[![CI](https://github.com/Muqsit/php-thinkit/actions/workflows/ci.yml/badge.svg)](https://github.com/Muqsit/php-thinkit/actions/workflows/ci.yml)

Think-Kit is a library that offers a generic machine learning implementation.


## Example Usage
```php
$training_input = Matrix::create([
    [0, 0, 1],
    [1, 1, 1],
    [1, 0, 1],
    [0, 0, 1]
]);
$training_output = Matrix::create([[0, 1, 1, 0]])->transpose();

$model = SimpleNeuralNetworkModel::create(iterations: 10_000)->transpose());
$model->train($training_input, $training_output);
$model->predict(Matrix::create([[1, 0, 0]])); // [[0.99991188]]
```
