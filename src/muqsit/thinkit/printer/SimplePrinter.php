<?php

declare(strict_types=1);

namespace muqsit\thinkit\printer;

use InvalidArgumentException;
use muqsit\thinkit\Matrix;
use muqsit\thinkit\Printer;
use function array_map;
use function implode;
use function str_pad;
use const PHP_EOL;

final class SimplePrinter implements Printer{

	public function __construct(
		/** @readonly */ public string $char_eol = PHP_EOL
	){}

	public function print(object $object) : string{
		if($object instanceof Matrix){
			return $this->printMatrix($object);
		}
		throw new InvalidArgumentException("Cannot print object " . $object::class);
	}

	public function printMatrix(Matrix $matrix) : string{
		if($matrix->rows === 0 || $matrix->columns === 0){
			return "[]";
		}

		$values = array_map(fn($row) => array_map(fn($value) => sprintf("%.8f", $value), $row), $matrix->values);
		$max_length = max(array_map(fn($row) => max(array_map("strlen", $row)), $values));
		$values = array_map(fn($row) => array_map(fn($value) => str_pad($value, $max_length), $row), $values);
		$values = array_map(fn($row) => "[" . implode(", ", $row) . "]", $values);
		return "[" . implode($this->char_eol . " ", $values) . "]";
	}
}