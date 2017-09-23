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
        $this->assertInstanceOf('\Generator', $first50, 'take() returns a Generator');

        $acc = [];
        foreach ($first50 as $n) {
            $acc[] = $n;
        }

        $this->assertSame(
            range(24, 24 + 49),
            $acc,
            'Take returns the first x numbers'
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
            iterator_to_array($squared->take(10)),
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
            iterator_to_array($evens->take(10)),
            'Iterating the filtered collection gives you mapped results. Note that take() still
                returns the right amount of items, the count is applied _after_ filtering'
        );

        $this->assertEmpty(
            iterator_to_array($numbers->filter('is_string')->take(20)),
            'The result will be empty when nothing matches the predicate'
        );
    }

}
