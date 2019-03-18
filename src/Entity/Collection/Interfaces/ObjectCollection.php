<?php


namespace App\Entity\Collection\Interfaces;


use App\Entity\Interfaces\Entity;

interface ObjectCollection extends ArrayCollection
{
    public function __construct(array $elements = []);

    public function isValid($values): bool;

    public function offsetSet($offset, $value): void;

    public function set($key, $value): void;

    public function addUnique($element): bool;

    public function add(Entity $element): void;
}