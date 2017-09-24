<?php
namespace LazyCollection;


class Subset extends LazyCollection {

    /**
     * @var LazyCollection
     */
    protected $collection;

    /**
     * @var int
     */
    protected $start;

    /**
     * @var int
     */
    protected $length;

    /**
     * @param LazyCollection $collection
     * @param int            $start
     * @param int            $length
     * @return void
     */
    public function __construct(LazyCollection $collection, int $start, int $length) {
        $this->collection = $collection;
        $this->start = $start;
        $this->length = $length;
        if ($start < 0) {
            throw new \InvalidArgumentException("'start' must be greater than zero");
        }
        if ($length <= 0) {
            throw new \InvalidArgumentException("'length' must be greater than zero");
        }
    }

    public function all(): \Generator {
        $iterations = -1;
        foreach ($this->collection->all() as $n) {
            $iterations++;
            if ($iterations < $this->start) {
                continue;
            }
            if ($iterations >= ($this->start + $this->length)) {
                break;
            }
            yield $n;
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
