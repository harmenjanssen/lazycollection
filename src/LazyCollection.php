<?php
namespace LazyCollection;

use LazyCollection\Mapped;
use LazyCollection\Filtered;

/**
 * @package  LazyCollection
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
abstract class LazyCollection {

    /**
     * The initial iteration value.
     *
     * @return int
     */
    abstract public function start();

    /**
     * Wether iterating is done.
     *
     * @param int $iteration
     * @return bool
     */
    abstract public function done($iteration): bool;

    /**
     * Get the value for the given iteration.
     *
     * @param int $iteration
     * @return mixed
     */
    abstract public function value($iteration);

    /**
     * Get the _next_ iteration value.
     *
     * @param int $iteration
     * @return int
     */
    abstract public function next($iteration);


    /**
     * Main entry point in getting the generator. You would foreach over this.
     *
     * @return \Generator
     */
    public function all(): \Generator {
        $iteration = $this->start();
        while (!$this->done($iteration)) {
            yield $this->value($iteration);
            $iteration = $this->next($iteration);
        }
    }

    /**
     * Convenience method to grab just the first value.
     *
     * @return mixed
     */
    public function first() {
        return $this->all()->current();
    }

    /**
     * Take a fixed amount of values.
     *
     * @param int $amount
     * @return \Generator
     */
    public function take(int $amount): \Generator {
        $iterations = 0;
        foreach ($this->all() as $n) {
            if ($iterations >= $amount) {
                break;
            }
            yield $n;
            $iterations++;
        }
    }

    /**
     * Grab a Mapped collection, based on the current collection.
     *
     * @param callable $transformer
     * @return Mapped
     */
    public function map(callable $transformer): Mapped {
        return new Mapped($this, $transformer);
    }

    /**
     * Grab a Filtered collection, based on the current collection.
     *
     * @param callable $filter
     * @return Filtered
     */
    public function filter($predicate): Filtered {
        return new Filtered($this, $predicate);
    }

}