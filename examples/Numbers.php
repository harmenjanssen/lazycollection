<?php
namespace LazyCollection\Examples;

use LazyCollection\LazyCollection;

/**
 * Infinite collection of numbers.
 * Will just keep yielding the next ad infinitum.
 *
 * @package  LazyCollection
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
class Numbers extends LazyCollection {

    /**
     * Starting value.
     *
     * @var int
     */
    protected $_start = 0;

    /**
     * Convenience method to grab an instance.
     *
     * @param int $start
     * @return Numbers
     */
    static public function from(int $start): Numbers {
        return new Numbers($start);
    }

    /**
     * @param int $start
     * @return void
     */
    public function __construct(int $start) {
        $this->_start = $start;
    }

    /**
     * The initial iteration value.
     *
     * @return int
     */
    public function start() {
        return $this->_start;
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
        return $iteration;
    }

    /**
     * Get the _next_ iteration value.
     *
     * @param int $iteration
     * @return int
     */
    public function next($iteration) {
        return $iteration + 1;
    }

}
