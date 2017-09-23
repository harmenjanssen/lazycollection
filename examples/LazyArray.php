<?php
namespace LazyCollection\Examples;

use LazyCollection\LazyCollection;

/**
 * A simple array implementation.
 * This just reads the array data in order, nothing special here.
 *
 * @package  LazyCollection
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
class LazyArray extends LazyCollection {

    /**
     * The internal array.
     *
     * @var array
     */
    protected $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * The initial iteration value.
     *
     * @return int
     */
    public function start() {
        return 0;
    }

    /**
     * Wether iterating is done.
     *
     * @param int $iteration
     * @return bool
     */
    public function done($iteration): bool {
        return $iteration >= count($this->data);
    }

    /**
     * Get the value for the given iteration.
     *
     * @param int $iteration
     * @return mixed
     */
    public function value($iteration) {
        return $this->data[$iteration];
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
