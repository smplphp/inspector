<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Traversable;

final class StructureProperties implements StructurePropertyCollection
{
    private Structure $structure;

    /**
     * @var array<string, \Smpl\Inspector\Contracts\Property>
     */
    private array $properties;

    /**
     * @param array<string, \Smpl\Inspector\Contracts\Property> $properties
     */
    public function __construct(Structure $structure, array $properties)
    {
        $this->structure  = $structure;
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

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function get(string $name): ?Property
    {
        return $this->properties[$name];
    }

    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    public function filter(PropertyFilter $filter): self
    {
        return new self(
            $this->getStructure(),
            array_filter($this->properties, $filter->check(...))
        );
    }
}