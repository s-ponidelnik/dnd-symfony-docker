<?php


namespace App\Entity\Collection\Interfaces;


interface ArrayCollection extends Collection
{
    public function __construct(array $elements = []);

    public function toArray();

    public function first();

    public function last();

    public function key();

    public function next();

    public function current();

    public function remove($key);

    public function removeElement($element);

    public function offsetExists($offset);


    public function offsetGet($offset);

    public function offsetSet($offset, $value);

    public function offsetUnset($offset);

    public function containsKey($key);


    public function contains($element);

    public function exists(Closure $p);

    public function indexOf($element);

    public function get($key);


    public function getKeys();

    public function getValues();

    public function count();

    public function set($key, $value);

    public function isEmpty();

    public function getIterator();

    public function map(Closure $func);

    public function filter(Closure $p);

    public function forAll(Closure $p);


    public function partition(Closure $p);


    public function __toString();

    public function clear();

    public function slice($offset, $length = null);
}