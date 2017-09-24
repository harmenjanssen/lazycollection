# LazyCollection

## What's this?

A `LazyCollection` class that takes care of creating a [generator](http://php.net/manual/en/language.generators.php) for your collection.  
Generators don't work with the standard `map`, `filter` and `reduce` functions in PHP.  
This is a shame of course because you lose a lot of handy abstractions. This package implements those methods lazily.

Consider the following example with traditional arrays:

```php
$square = function($x) {
  return $x * $x;
};
$isEven = function($x) {
  return $x % 2 === 0;
};

$highestEvenSquare = array_filter(
    array_map(
        $square,
        range(30, 0)
    ),
    $isEven
)[0];
```

This requires the entire range to be created, mapped to squares, and filtered before we can grab the first item.

With `LazyCollection`, this could be implemented like this:

```php
$lazyCollection = new LazyCollection\Examples\LazyArray(range(29, 0));
$lazyCollection->map($square)
    ->filter($isEven)
    ->first();
```

Of course, the initial range will be created in memory, but only the first two items would need to be squared and filtered.
Since the second item, 28, is a match to our filter function (28 * 28 results in the even number 784), `first()` will just stop there.
The rest of the squares won't be computed.  

It's easy to imagine a collection where every next item is computed on the spot, in which case the original array doesn't even have to be 
created in memory (for instance: search a large database or file until a fixed number of matches is found).

## Installation

Install using Composer: 

```
composer require harmenjanssen/lazycollection
```

## Usage

`all()`
- This method returns the actual `Generator`:

```php
$numbers = LazyCollection\Examples\Numbers::from(1);
foreach ($numbers->take(100)->all() as $n) {
    echo $n . "\n";
}
// will echo numbers 1 through 100
```

`first()`
- Take the first item of the collection.

`take(int $amount): Subset`
- Limits the collection to the given amount. Returns a new `LazyCollection` of type `Subset`. 
  See example under `all()`.

`slice(int $start, int $length): Subset`
- Take a subset of `$length` items of the collection starting at `$start`.

```php
$numbers = LazyCollection\Examples\Numbers::from(1);
$fifty = $numbers->slice(50, 10);

iterator_to_array($fifty); // [50, 51, ... 60]
```

`map(callable $transformer): Mapped`
- Add a mapping function to the collection. Returns a new `LazyCollection` of type `Mapped`.
    
```php
$numbers = LazyCollection\Examples\Numbers::from(1);
$squares = $numbers->map(function($x) {
    return $x * $x;
});

iterator_to_array($squares->take(10)); // [1, 4, 9, 16, 25... 100]
```

`filter(callable $predicate): Filtered`
- Add a filter function to the collection. Returns a new `LazyCollection` of type `Filtered`.

```php
$numbers = LazyCollection\Examples\Numbers::from(1);
$evenStevens = $numbers->filter(function($x) {
    return $x % 2 === 0;
});

iterator_to_array($evenStevens->take(10)); // [2, 4, 6, 8, 10... 20]
```

`reduce(callable $reducer, $seed)`
- Reduce the collection to a single value.

```php
$numbers = LazyCollection\Examples\Numbers::from(1)->take(10);
$sum = $numbers->reduce(function($a, $b) {
    return $a + $b;
}, 0); // 55
```

## Implement your own lazy collections

Create a subclass of `LazyCollection`, implementing the 4 required methods:

`start()`
- Returns the initial iteration. Note that this is not the same as an iteration's value, 
  nor does it have to be numeric. You determine how you iterate your collection and what 
  data you need to progress through it.
  See the Fibonacci example for a collection that uses an array to track progress through 
  the collection

`next($iteration)`
- Returns the next iteration based on the previous iteration.
  Note that the return type and the type of its parameter are the same as those of `start()`, most likely.

`done(): bool`
- Wether iteration is done. 

`value($iteration)`
- Return a value based on the given iteration.


## Infinite collections

Infinite collection are elegant solutions to computational patterns. 
Both the [Numbers](https://github.com/harmenjanssen/lazycollection/blob/master/examples/Numbers.php) sequence and the [Fibonacci](https://github.com/harmenjanssen/lazycollection/blob/master/examples/Fibonacci.php) sequence are examples of infinite collections.  
You can project a range of values, but there's no natural end.

Even though iterating over these collections is no problem, you have to make sure you bring iteration to a halt yourself, or you'll run out of memory.
`take()` is a handy method to use in this case.

```php
$fibonacci = new LazyCollection\Examples\Fibonacci;
iterator_to_array($fibonacci->take(10)->all()); // [1, 1, 2, 3, 5, 8, 13, 21, 34, 55]
```

Also consider this pitfall: when filtering an infinite collection by a predicate _that matches nothing_, the filter will 
not return an empty collection, as it would with regular arrays. It will run out of memory since it never ends.

```php
// This will never resolve:

$fibonacci = new LazyCollection\Examples\Fibonacci;
$fibonacci->filter('is_string')->take(10);
```

Note also that you cannot use `iterator_to_array` to flatten the iterator into an array. Again, this would never end.

## Todos

- Implement `until()`
- Implement `find()`

## Inspiration

I picked up the concept of functional iterators from the wonderful book [Javascript Allongé](https://leanpub.com/javascriptallongesix/), by [Reginald Braithwaite](https://github.com/raganwald).

Lots of interesting languages support the concept of lazy (possibly infinite) sequences, like Haskell and Clojure.
