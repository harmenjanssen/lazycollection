<?php
namespace LazyCollection\Examples;

use LazyCollection\LazyCollection;

/**
 * Infinite collection of Fibonacci numbers.
 * Will just keep yielding the next ad infinitum.
 *
 * @package  LazyCollection\Examples
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
class Fibonacci extends LazyCollection {

    /**
     * The initial iteration value.
     *
     * @return int
     */
    public function start() {
        return [0, 1];
    }

    /**
     * An infinite collection, so this never returns true.
     *
     * @param int $iteration
     * @return bool
     */
    public function done($iteration): bool {
        return false;
    }

    /**
     * Get the value for the given iteration.
     *
     * @param int $iteration
     * @return mixed
     */
    public function value($iteration) {
        return $iteration[1];
    }

    /**
     * Get the _next_ iteration value.
     *
     * @param int $iteration
     * @return int
     */
    public function next($iteration) {
        list($a, $b) = $iteration;
        return [$b, $a + $b];
    }

}
