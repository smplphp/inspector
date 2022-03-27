<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Concerns\CollectionForStructure;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructurePropertyCollection;

final class StructureProperties extends Properties implements StructurePropertyCollection
{
    use CollectionForStructure;

    public static function for(Structure $structure, PropertyCollection $properties): self
    {
        return new self($structure, $properties->values());
    }

    private Structure $structure;

    /**
     * @param list<\Smpl\Inspector\Contracts\Property> $properties
     */
    public function __construct(Structure $structure, array $properties)
    {
        $this->structure = $structure;
        parent::__construct($properties);
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function get(string $name): ?Property
    {
        return parent::get($this->normaliseKey($name));
    }

    public function has(string $name): bool
    {
        return parent::has($this->normaliseKey($name));
    }

    public function asBase(): PropertyCollection
    {
        return new Properties($this->values());
    }
}