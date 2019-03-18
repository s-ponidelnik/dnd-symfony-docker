<?php

namespace App\Entity\Enum;

abstract class Enum
{
    /** @var array $cache */
    protected static $cache = [];

    /** @var array $objectCache */
    protected static $objectCache = [];

    /** @var string $value */
    private $value = '';

    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new \UnexpectedValueException("Value '$value' is not part of the enum " . get_called_class());
        }

        $class = get_called_class();
        static::$objectCache[$class][$value] = $this;

        $this->value = $value;
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, static::toArray(), true);
    }

    public static function toArray(): array
    {
        $class = get_called_class();
        if (!array_key_exists($class, static::$cache)) {
            try {
                $reflection = new \ReflectionClass($class);
                static::$cache[$class] = $reflection->getConstants();
            } catch (\Throwable $e) {
                static::$cache[$class] = [];
            }
        }

        return static::$cache[$class];
    }

    public static function values(): array
    {
        $values = [];
        foreach (static::toArray() as $key => $value) {
            $values[$key] = static::getInstance($value);
        }

        return $values;
    }

    public static function getInstance(string $value)
    {
        $class = get_called_class();
        if (isset(static::$objectCache[$class][$value])) {
            return static::$objectCache[$class][$value];
        }

        return new static($value);
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    final public function equals(Enum $enum): bool
    {
        return $this->getValue() === $enum->getValue() && get_called_class() == get_class($enum);
    }
}
