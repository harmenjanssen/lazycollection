<?php
namespace LazyCollection\Tests;

use PHPUnit\Framework\TestCase;
use LazyCollection\Examples\Fibonacci;

/**
 * @package  LazyCollection
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
class FibonacciTest extends TestCase {

    public function testFibonacci() {
        $twentyFibonacci = (new Fibonacci)->take(20);
        $fixture = [
            1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, 144, 233, 377, 610, 987, 1597, 2584, 4181, 6765
        ];
        $this->assertSame(
            $fixture,
            iterator_to_array($twentyFibonacci->all())
        );
    }

}
