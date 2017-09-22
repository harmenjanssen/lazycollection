<?php
namespace Lazy;

trait Iterator {

    public function first() {
        return $this->next();
    }

    public function take(int $amount): \Generator {
    /**
     * @todo Would love it if rewind was unneccessary. But also this brings it back to 0 even though
     * we started at 10.
     */
        $this->rewind();
        $n = 0;
        while ($n < $amount || $this->done()) {
            $n += 1;
            yield $this->next();
        }
    }

}

class FilteredIterator {
    use Iterator;

    protected $_collection;
    protected $_predicate;

    public function __construct(LazyCollection $collection, callable $predicate) {
        $this->_collection = $collection;
        $this->_predicate = $predicate;
    }

    public function next() {
        while (!$this->_collection->done()) {
            $next = $this->_collection->next();
            if ($this->_predicate($next)) {
                yield $next;
            }
        }
    }

}

abstract class LazyCollection {
    use Iterator;

    abstract public function done();
    abstract public function next();
    abstract public function rewind();

    public function filter(callable $predicate) {
        return new FilteredIterator($this->rewind(), $predicate);
    }

}

class Numbers extends LazyCollection {

    protected $_index = 0;

    /**
     * @todo Would love it if rewind was unneccessary.....
     */
    public function rewind() {
        $this->_index = 0;
    }

    public function done() {
        // infinite collection
        return false;
    }

    public function next() {
        $result = $this->_index;
        $this->_index += 1;
        return $result;
    }

    private function __construct(int $from) {
        $this->_index = $from;
    }

    static public function from(int $from) {
        return new Numbers($from);
    }

}

function out($in) {
    echo "{$in}\n";
}

out("Working with numbers from 10");
$numbers = Numbers::from(10);

out("First:");
out($numbers->first());

out("Take 5:");
foreach ($numbers->take(5) as $n) {
    out($n);
}

out("Filter evens numbers:");
$isEven = function (int $n): bool {
    return $n % 2 === 0;
};
foreach ($numbers->filter($isEven)->take(5) as $n) {
    out($n);
}
