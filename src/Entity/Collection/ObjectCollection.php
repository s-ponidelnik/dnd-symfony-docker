<?php

namespace App\Entity\Collection;
use \App\Entity\Collection\Interfaces\ObjectCollection as ObjectCollectionInterface;
class ObjectCollection extends ArrayCollection implements ObjectCollectionInterface
{
    /** @var string $className */
    private $className;

    public function __construct(string $className, array $elements = [])
    {
        $this->className = $className;
        if ($elements && !$this->isValid($elements)) {
            $this->throwInvalidObjectClassException();
        }

        parent::__construct($elements);
    }

    public function isValid($values): bool
    {
        if (!$values) {
            return false;
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            if (!$value instanceof $this->className) {
                return false;
            }
        }

        return true;
    }

    protected function throwInvalidObjectClassException(): void
    {
        throw new \InvalidArgumentException('Elements must be of type '.$this->className);
    }

    public function offsetSet($offset, $value): void
    {
        if (!$this->isValid($value)) {
            $this->throwInvalidObjectClassException();
        }

        parent::offsetSet($offset, $value);
    }

    public function set($key, $value): void
    {
        if (!$this->isValid($value)) {
            $this->throwInvalidObjectClassException();
        }

        parent::set($key, $value);
    }

    public function addUnique($element): bool
    {
        if ($this->contains($element)) {
            return false;
        }

        $this->add($element);

        return true;
    }

    public function add($element): void
    {
        if (!$this->isValid($element)) {
            $this->throwInvalidObjectClassException();
        }

        parent::add($element);
    }
}
