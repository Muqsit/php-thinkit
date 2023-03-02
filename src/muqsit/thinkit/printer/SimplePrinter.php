<?php

declare(strict_types=1);

namespace muqsit\thinkit\printer;

use InvalidArgumentException;
use muqsit\thinkit\Matrix;
use muqsit\thinkit\Printer;
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
		$values = [];
		$space = 0;
		for($i = 0; $i < $matrix->rows; $i++){
			for($j = 0; $j < $matrix->columns; $j++){
				$values[$i][$j] = sprintf("%.8f", $matrix->values[$i][$j]);
				$space_this = strlen($values[$i][$j]);
				if($space_this > $space){
					$space = $space_this;
				}
			}
		}

		if(count($values) === 0){
			return "[]";
		}

		$result = [];
		for($i = 0; $i < $matrix->rows; $i++){
			$line = "[";
			for($j = 0; $j < $matrix->columns; $j++){
				$value = $values[$i][$j];
				$line .= $value;
				if($j !== $matrix->columns - 1){
					$line .= ",";
				}
				$line .= str_repeat(" ", $space - strlen($value));
			}
			$line .= "]";
			$result[] = $line;
		}

		if(count($result) === 1){
			return $result[0];
		}

		$print = "[";
		$print .= array_shift($result) . $this->char_eol;
		foreach($result as $line){
			$print .= " " . $line . $this->char_eol;
		}
		$print[strlen($print) - 1] = "]";
		return $print;
	}
}