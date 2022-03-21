<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyCollection;
use Smpl\Inspector\Contracts\PropertyFilter;
use Traversable;

class Properties implements PropertyCollection
{
    /**
     * @var \Smpl\Inspector\Contracts\Property[]
     */
    protected array $properties;

    /**
     * @param array<string, \Smpl\Inspector\Contracts\Property> $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return \Traversable<string, \Smpl\Inspector\Contracts\Property>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->properties);
    }

    public function count(): int
    {
        return count($this->properties);
    }

    public function get(string $name): ?Property
    {
        return $this->properties[$name];
    }

    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    public function filter(PropertyFilter $filter): static
    {
        return new self(
            array_filter($this->properties, $filter->check(...))
        );
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
}