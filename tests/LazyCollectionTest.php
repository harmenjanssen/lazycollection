<?php
namespace LazyCollection\Tests;

use PHPUnit\Framework\TestCase;
use LazyCollection\Examples\LazyArray;
use LazyCollection\Examples\Numbers;

/**
 * @package  LazyCollection
 * @author   Harmen Janssen <harmen@whatstyle.net>
 */
class LazyCollectionTest extends TestCase {

    protected $functionLog = [];

    public function setUp() {
        $this->functionLog = [];
    }

    public function testAll() {
        $lazyArray = new LazyArray(['foo', 'bar', 'baz']);
        $all = $lazyArray->all();
        $this->assertInstanceOf('\Generator', $all, 'all() returns a Generator');

        $acc = [];
        foreach ($all as $out) {
            $acc[] = $out;
        }
        $this->assertSame(['foo', 'bar', 'baz'], $acc, 'all() returns the items in order');

        $emptyArray = new LazyArray([]);
        $this->assertSame(
            [],
            iterator_to_array($emptyArray->all()),
            'An empty array is no problem'
        );
    }

    public function testFirst() {
        $lazyArray = new LazyArray(['foo', 'bar', 'baz']);
        $this->assertSame(
            'foo',
            $lazyArray->first(),
            'first() returns the first item'
        );

        $this->assertNull(
            (new LazyArray([]))->first(),
            'first() returns null if the collection is empty'
        );
    }

    public function testTake() {
        $numbers = Numbers::from(24);
        $first50 = $numbers->take(50);
        $this->assertInstanceOf('LazyCollection\Subset', $first50, 'take() returns a Subset');

        $acc = [];
        foreach ($first50() as $n) {
            $acc[] = $n;
        }

        $this->assertCount(50, $acc);
        $this->assertSame(
            range(24, 24 + 49),
            $acc,
            'Take returns the first x numbers'
        );
    }

    public function testSlice() {
        $veggies = new LazyArray(['carrot', 'cabbage', 'zucchini', 'turnip']);
        $slice = $veggies->slice(1, 2);
        $this->assertSame(
            ['cabbage', 'zucchini'],
            iterator_to_array($slice->all())
        );
    }

    public function testMap() {
        $numbers = Numbers::from(1);
        $squared = $numbers->map(function($x) {
            return $x * $x;
        });
        $this->assertInstanceOf(
            'LazyCollection\Mapped',
            $squared,
            'map() returns a Mapped collection'
        );

        $this->assertSame(
            [1, 4, 9, 16, 25, 36, 49, 64, 81, 100],
            iterator_to_array($squared->take(10)->all()),
            'Iterating the mapped collection gives you mapped results'
        );
    }

    public function testFilter() {
        $numbers = Numbers::from(1);
        $evens = $numbers->filter(function($x) {
            return $x % 2 === 0;
        });
        $this->assertInstanceOf(
            'LazyCollection\Filtered',
            $evens,
            'filter() returns a Filtered collection'
        );

        $this->assertSame(
            [2, 4, 6, 8, 10, 12, 14, 16, 18, 20],
            iterator_to_array($evens->take(10)->all()),
            'Iterating the filtered collection gives you mapped results. Note that take() still
                returns the right amount of items, the count is applied _after_ filtering'
        );

        $numbers = new LazyArray([1, 2, 3, 4, 5]);
        $this->assertEmpty(
            iterator_to_array($numbers->filter('is_string')->take(20)->all()),
            'The result will be empty when nothing matches the predicate'
        );
    }

    public function testCombinedMapAndFilter() {
        $collection = new LazyArray(
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
        );
        $firstSquareOver80 = $collection->map(function($x) {
            return $x * $x;
        })->filter(function($x) {
            return $x > 80;
        })->first();

        $this->assertSame(
            81,
            $firstSquareOver80,
            'map() and filter() can be combined wonderfully'
        );

        /**
         * Let's prove it's lazy by logging our actions:
         */
        $collection->map(function($x) {
            $this->functionLog[] = "mapping $x";
            return $x * $x;
        })->filter(function($x) {
            $this->functionLog[] = "filtering $x";
            return $x > 40;
        })->first();

        $this->assertSame(
            [
                'mapping 1',
                'filtering 1',
                'mapping 2',
                'filtering 4',
                'mapping 3',
                'filtering 9',
                'mapping 4',
                'filtering 16',
                'mapping 5',
                'filtering 25',
                'mapping 6',
                'filtering 36',
                'mapping 7',
                'filtering 49',
            ],
            $this->functionLog,
            'It maps only the data that\'s necessary. You never have to compute all mappings unless
                you would ask for all()'
        );

    }

    public function testReduce() {
        $numbers = new LazyArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $plus = function($a, $b) {
            return $a + $b;
        };

        $this->assertSame(
            55,
            $numbers->reduce($plus, 0)
        );

        $veggies = new LazyArray(['carrot', 'cabbage', 'zucchini', 'turnip', 'banana', 'apple']);
        $longest = function($a, $b) {
            return $a > $b ? $a : $b;
        };

        // Oops, some fruit slipped in at the end.
        $veggies = $veggies->take(4);

        $this->assertSame(
            'zucchini',
            $veggies->reduce($longest, '')
        );
    }

}
