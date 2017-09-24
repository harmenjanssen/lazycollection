<?php
namespace LazyCollection;

/**
 * Represents a filtered iterator. Filtered is in itself also a LazyCollection,
 * allowing you to chain further and created mapped, filtered collections.
 *
 * @package  LazyCollection
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
class Filtered extends LazyCollection {

    /**
     * @var LazyCollection
     */
    protected $collection;

    /**
     * @var callable
     */
    protected $predicate;

    /**
     * @param LazyCollection $collection  All iteration methods are proxied to this.
     * @param callable       $predicate   The filter function
     * @return void
     */
    public function __construct(LazyCollection $collection, callable $predicate) {
        $this->collection = $collection;
        $this->predicate = $predicate;
    }

    public function all(): \Generator {
        foreach ($this->collection->all() as $n) {
            if (call_user_func($this->predicate, $n)) {
                yield $n;
            }
        }
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
        return $this->collection->value($iteration);
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

