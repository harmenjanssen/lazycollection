<?php
namespace Lazy;

abstract class LazyCollection {

    abstract public function start();

    abstract public function done($iteration): bool;

    abstract public function value($iteration);

    abstract public function next($iteration);


    public function all(): \Generator {
        $iteration = $this->start();
        while (!$this->done($iteration)) {
            yield $this->value($iteration);
            $iteration = $this->next($iteration);
        }
    }

    public function first() {
        return $this->all()->current();
    }

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

    public function map($transformer): Mapped {
        return new Mapped($this, $transformer);
    }

}

class Mapped extends LazyCollection {

    protected $collection;

    protected $transformer;

    public function __construct(LazyCollection $collection, callable $transformer) {
        $this->collection = $collection;
        $this->transformer = $transformer;
    }

    public function start() {
        return $this->collection->start();
    }

    public function done($iteration): bool {
        return $this->collection->done($iteration);
    }

    public function value($iteration) {
        return ($this->transformer)($this->collection->value($iteration));
    }

    public function next($iteration) {
        return $this->collection->next($iteration);
    }

}

class Filtered extends LazyCollection {

    protected $collection;

    protected $predicate;

    public function __construct(LazyCollection $collection, callable $predicate) {
        $this->collection = $collection;
        $this->predicate = $predicate;
    }

    public function start() {
        return $this->collection->start();
    }

    public function done($iteration): bool {
        return $this->collection->done($iteration);
    }

    public function value($iteration) {
        return $this->collection->value($iteration);
    }

    public function next($iteration) {
        return $this->collection->next($iteration);
    }

}

class xList extends LazyCollection {

    protected $_data;

    public function __construct(array $data) {
        $this->_data = $data;
    }

    public function start() {
        return 0;
    }

    public function done($iteration): bool {
        return $iteration >= count($this->_data);
    }

    public function value($iteration) {
        return $this->_data[$iteration];
    }

    public function next($iteration) {
        return $iteration + 1;
    }

    public function filter($predicate) {
        return [
            'iterator' => function() use ($predicate) {
                foreach ($this->all() as $i) {
                    if ($predicate($i)) {
                        yield $i;
                    }
                }
            }
        ];
    }

}

/*
class Numbers extends LazyCollection {

    protected $_start = 0;

    public function init() {
        return $this->_start;
    }

    public function done($value) {
        // infinite collection
        return false;
    }

    public function next($prev) {
        return $prev + 1;
    }

    private function __construct(int $start) {
        $this->_start = $start;
    }

    static public function from(int $start) {
        return new Numbers($start);
    }

}
 */

function out($in) {
    echo "{$in}\n";
}

$square = function ($n) {
    return $n * $n;
};

$isEven = function (int $n): bool {
    return $n % 2 === 0;
};

$list = new xList([0, 1, 2, 3, 4, 5]);
foreach ($list->all() as $n) {
    out($n);
}

out('squares:');
foreach ($list->map($square)->all() as $n) {
    out($n);
}

out('even numbers:');
foreach ($list->filter($isEven)['iterator']() as $n) {
    out($n);
}

out('FIRST!');
out($list->first());

out('Take 5');
foreach ($list->take(5) as $n) {
    out($n);
}

out('Take 5');
foreach ($list->take(5) as $n) {
    out($n);
}


/*
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

out('Squares numbers');
$square = function ($n) {
    return $n * $n;
};
foreach ($numbers->map($square)->take(5) as $n) {
    out($n);
}
 */
