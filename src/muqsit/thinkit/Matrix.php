<?php

declare(strict_types=1);

namespace muqsit\thinkit;

use Closure;
use InvalidArgumentException;
use muqsit\thinkit\random\MersenneTwisterRandom;
use function count;
use function round;
use function sprintf;
use const PHP_ROUND_HALF_UP;

final class Matrix{

	/**
	 * Creates a matrix of a given size with random values.
	 * Equivalent of numpy.random.random(size=(rows, columns)).
	 *
	 * @param int<0, max> $rows row size of the matrix
	 * @param int<0, max> $columns column size of the matrix
	 * @param Random|null $random the randomizer used to generate the matrix values
	 * @return self
	 */
	public static function random(int $rows, int $columns, ?Random $random = null) : self{
		self::validate($rows, $columns);
		if($rows === 0 || $columns === 0){
			return new self($rows, $columns, []);
		}

		$random ??= MersenneTwisterRandom::instance();
		$values = [];
		for($i = 0; $i < $rows; $i++){
			$row = [];
			for($j = 0; $j < $columns; $j++){
				$row[] = $random->generateRange(0, 100_000_000) * 1e-8;
			}
			$values[] = $row;
		}
		return new self($rows, $columns, $values);
	}

	/**
	 * Creates a matrix from raw values represented by a 2D array.
	 *
	 * @param list<list<float>> $values
	 * @return self
	 */
	public static function create(array $values) : self{
		$rows = count($values);
		$columns = $rows === 0 ? 0 : count($values[0] ?? throw new InvalidArgumentException(sprintf("Supplied value is not a list (row index 0 not found in a matrix of %d row(s))", $rows)));
		for($i = 0; $i < $rows; $i++){
			$count_c = count($values[$i] ?? throw new InvalidArgumentException(sprintf("Supplied value is not a list (row index %d not found in a matrix of %d row(s))", $i, $rows)));
			$columns === $count_c || throw new InvalidArgumentException(sprintf("Rows in a matrix must have the same number of elements (length of first row (%d) is not equal to the length of [%d] row (%d))", $columns, $i, $count_c));
			for($j = 0; $j < $columns; $j++){
				isset($values[$i][$j]) || throw new InvalidArgumentException(sprintf("Supplied value is not a list (column index %d in row %d not found in a matrix of %d column(s))", $j, $i, $columns));
			}
		}
		if($rows === 0 || $columns === 0){
			return new self($rows, $columns, []);
		}
		return new self($rows, $columns, $values);
	}

	/**
	 * Creates a matrix of a given size initialized with the given value.
	 *
	 * @param int<0, max> $rows row size of the matrix
	 * @param int<0, max> $columns column size of the matrix
	 * @param int<0, max>|float $value the value to initialize with
	 * @return self
	 */
	public static function full(int $rows, int $columns, int|float $value) : self{
		self::validate($rows, $columns);
		if($rows === 0 || $columns === 0){
			return new self($rows, $columns, []);
		}
		$values = array_fill(0, $rows, array_fill(0, $columns, $value));
		return new self($rows, $columns, $values);
	}

	private static function validate(int $rows, int $columns) : void{
		$rows >= 0 || throw new InvalidArgumentException(sprintf("Number of rows must be >= 0, got %d", $rows));
		$columns >= 0 || throw new InvalidArgumentException(sprintf("Number of columns must be >= 0, got %d", $columns));
	}

	/**
	 * @param int<0, max> $rows
	 * @param int<0, max> $columns
	 * @param list<list<float>> $values
	 */
	private function __construct(
		/** @readonly */ public int $rows,
		/** @readonly */ public int $columns,
		/** @readonly */ public array $values
	){}

	public function equals(self $other) : bool{
		return $this->rows === $other->rows && $this->columns === $other->columns && $this->values === $other->values;
	}

	/**
	 * @param int<0, max> $precision
	 * @param PHP_ROUND_HALF_UP|PHP_ROUND_HALF_DOWN|PHP_ROUND_HALF_EVEN|PHP_ROUND_HALF_ODD $mode
	 * @return self
	 */
	public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP) : self{
		return $this->apply(fn($value) => round($value, $precision, $mode));
	}

	/**
	 * @param int<0, max> $rows
	 * @param int<0, max> $columns
	 * @return self
	 */
	public function broadcast(int $rows, int $columns) : self{
		$values = [];
		for($i = 0; $i < $rows; $i++){
			$row = [];
			for($j = 0; $j < $columns; $j++){
				$row[] = $this->values[$i % $this->rows][$j % $this->columns];
			}
			$values[] = $row;
		}
		return new self($rows, $columns, $values);
	}

	/**
	 * @param Closure(float $value) : float $function
	 * @return self
	 */
	public function apply(Closure $function) : self{
		$values = [];
		for($i = 0; $i < $this->rows; $i++){
			$row = [];
			for($j = 0; $j < $this->columns; $j++){
				$row[] = $function($this->values[$i][$j]);
			}
			$values[] = $row;
		}
		return new self($this->rows, $this->columns, $values);
	}

	/**
	 * @param self $rvalue
	 * @param Closure(float $lvalue, float $rvalue) : float $function
	 * @return self
	 */
	public function broadcastAndApply(self $rvalue, Closure $function) : self{
		if($this->rows !== $rvalue->rows || $this->columns !== $rvalue->columns){
			if($this->columns === $rvalue->rows){
				$b = $this->broadcast($rvalue->rows, $this->columns);
				$b_rvalue = $rvalue->broadcast($rvalue->rows, $this->columns);
				return $b->broadcastAndApply($b_rvalue, $function);
			}
			throw new InvalidArgumentException(sprintf("Cannot broadcast and apply a %dx%d matrix with another %dx%d matrix", $this->rows, $this->columns, $rvalue->rows, $rvalue->columns));
		}

		$values = [];
		for($i = 0; $i < $this->rows; $i++){
			$row = [];
			for($j = 0; $j < $this->columns; $j++){
				$row[] = $function($this->values[$i][$j], $rvalue->values[$i][$j]);
			}
			$values[] = $row;
		}
		return new self($this->rows, $this->columns, $values);
	}

	public function transpose() : self{
		$values = [];
		for($i = 0; $i < $this->columns; $i++){
			$row = [];
			for($j = 0; $j < $this->rows; $j++){
				$row[] = $this->values[$j][$i];
			}
			$values[] = $row;
		}
		return new self($this->columns, $this->rows, $values);
	}

	public function dot(self $rvalue) : self{
		if($this->columns !== $rvalue->rows){
			throw new InvalidArgumentException(sprintf("Cannot calculate dot product of a %dx%d matrix with another %dx%d matrix", $this->rows, $this->columns, $rvalue->rows, $rvalue->columns));
		}

		$values = [];
		for($i = 0; $i < $this->rows; $i++){
			$row = [];
			for($j = 0; $j < $rvalue->columns; $j++){
				$product = 0;
				for($k = 0; $k < $this->columns; $k++){
					$product += $this->values[$i][$k] * $rvalue->values[$k][$j];
				}
				$row[] = $product;
			}
			$values[] = $row;
		}
		return new self($this->rows, $rvalue->columns, $values);
	}

	public function add(self $rvalue) : self{
		return $this->broadcastAndApply($rvalue, fn($lvalue, $rvalue) => $lvalue + $rvalue);
	}

	public function subtract(self $rvalue) : self{
		return $this->broadcastAndApply($rvalue, fn($lvalue, $rvalue) => $lvalue - $rvalue);
	}

	public function multiply(self $rvalue) : self{
		return $this->broadcastAndApply($rvalue, fn($lvalue, $rvalue) => $lvalue * $rvalue);
	}
}