<?php
namespace LazyCollection;

/**
 * Represents a mapped iterator. Mapped is in itself also a LazyCollection, allowing you to chain
 * further and created mapped, filtered collections.
 *
 * @package  LazyCollection
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
class Mapped extends LazyCollection {

    /**
     * @var LazyCollection
     */
    protected $collection;

    /**
     * @var callable
     */
    protected $transformer;

    /**
     * @param LazyCollection $collection  All iteration methods are proxied to this.
     * @param callable       $transformer The map function
     * @return void
     */
    public function __construct(LazyCollection $collection, callable $transformer) {
        $this->collection = $collection;
        $this->transformer = $transformer;
    }

    /**
     * The initial iteration value.
     *
     * @return int
     */
    public function start() {
        return $this->collection->start();
    }

    /**
     * Wether iterating is done.
     *
     * @param int $iteration
     * @return bool
     */
    public function done($iteration): bool {
        return $this->collection->done($iteration);
    }

    /**
     * Get the mapped value for the given iteration.
     *
     * @param int $iteration
     * @return mixed
     */
    public function value($iteration) {
        return ($this->transformer)($this->collection->value($iteration));
    }

    /**
     * Get the _next_ iteration value.
     *
     * @param int $iteration
     * @return int
     */
    public function next($iteration) {
        return $this->collection->next($iteration);
    }

}

