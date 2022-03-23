<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\PropertyCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructurePropertyCollection;

final class StructureProperties extends Properties implements StructurePropertyCollection
{
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
}