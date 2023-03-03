<?php

declare(strict_types=1);

namespace muqsit\thinkit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MatrixTest extends TestCase{

	public function testEmptyMatrix() : void{
		$this->assertEquals(Matrix::full(rows: 0, columns: 0, value: 0), Matrix::create([]));
		$this->assertNotEquals(Matrix::full(rows: 1, columns: 0, value: 0), Matrix::create([]));
		$this->assertNotEquals(Matrix::full(rows: 1, columns: 1, value: 0), Matrix::create([]));
		$this->assertEmpty(Matrix::full(rows: 1, columns: 0, value: 1)->values);
		$this->assertEmpty(Matrix::full(rows: 0, columns: 1, value: 2)->values);
		$this->assertEmpty(Matrix::create([[]])->values);
		$this->assertEmpty(Matrix::create([[], []])->values);
	}

	public function testInvalidRowSizeFull() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Number of rows must be >= 0, got -1");
		Matrix::full(rows: -1, columns: 1, value: 0);
	}

	public function testInvalidColumnSizeFull() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Number of columns must be >= 0, got -1");
		Matrix::full(rows: 1, columns: -1, value: 0);
	}

	public function testValidSizeFull() : void{
		$this->assertEquals(Matrix::create([
			[0, 0],
			[0, 0],
			[0, 0]
		]), Matrix::full(rows: 3, columns: 2, value: 0));
		$this->assertEquals(Matrix::create([
			[1, 1, 1],
			[1, 1, 1]
		]), Matrix::full(rows: 2, columns: 3, value: 1));
		$this->assertEquals(Matrix::create([
			[2, 2, 2],
			[2, 2, 2]
		]), Matrix::full(rows: 2, columns: 3, value: 2));
	}

	public function testInvalidRowSizeRandom() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Number of rows must be >= 0, got -1");
		Matrix::random(rows: -1, columns: 1);
	}

	public function testInvalidColumnSizeRandom() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Number of columns must be >= 0, got -1");
		Matrix::random(rows: 1, columns: -1);
	}

	public function testValidSizeRandom() : void{
		$matrix = Matrix::random(rows: 1, columns: 1);
		$this->assertEquals(1, $matrix->rows);
		$this->assertEquals(1, $matrix->columns);
	}

	public function testInvalidGappedRow() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Supplied value is not a list (row index 0 not found in a matrix of 1 row(s))");
		Matrix::create([1 => []]);
	}

	public function testInvalidAssociativeRow() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Supplied value is not a list (row index 0 not found in a matrix of 1 row(s))");
		Matrix::create(["assoc" => []]);
	}

	public function testInvalidGapColumn() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Supplied value is not a list (column index 0 in row 0 not found in a matrix of 1 column(s))");
		Matrix::create([[1 => 0]]);
	}

	public function testInvalidAssociativeColumn() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Supplied value is not a list (column index 0 in row 0 not found in a matrix of 1 column(s))");
		Matrix::create([["assoc" => 0]]);
	}

	public function testInvalidBroadcastAndApply() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Cannot broadcast and apply a 3x2 matrix with another 3x3 matrix");
		$m1 = Matrix::full(rows: 3, columns: 2, value: 1);
		$m2 = Matrix::full(rows: 3, columns: 3, value: 1);
		$m1->broadcastAndApply($m2, fn($lvalue, $rvalue) => $lvalue + $rvalue);
	}

	public function testInvalidDotProduct() : void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Cannot calculate dot product of a 3x2 matrix with another 3x3 matrix");
		$m1 = Matrix::full(rows: 3, columns: 2, value: 1);
		$m2 = Matrix::full(rows: 3, columns: 3, value: 1);
		$m1->dot($m2);
	}

	public function testValidDotProduct() : void{
		$m1 = Matrix::full(rows: 2, columns: 3, value: 1);
		$m2 = Matrix::full(rows: 3, columns: 3, value: 2);
		$this->assertEquals(Matrix::full(2, 3, 6), $m1->dot($m2));

		$m1 = Matrix::full(rows: 2, columns: 4, value: 1);
		$m2 = Matrix::full(rows: 4, columns: 5, value: 2);
		$this->assertEquals(Matrix::full(2, 5, 8), $m1->dot($m2));

		$m1 = Matrix::full(rows: 0, columns: 1, value: 1);
		$m2 = Matrix::full(rows: 1, columns: 0, value: 2);
		$this->assertEquals(Matrix::full(0, 0, 0), $m1->dot($m2));

		$m1 = Matrix::full(rows: 1, columns: 0, value: 1);
		$m2 = Matrix::full(rows: 0, columns: 1, value: 2);
		$this->assertEquals(Matrix::full(1, 1, 0), $m1->dot($m2));
	}
}