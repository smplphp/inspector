<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\PropertyFilter;
use Smpl\Inspector\Contracts\Structure;

final class StructureProperties extends Properties
{
    private Structure $structure;

    /**
     * @param array<string, \Smpl\Inspector\Contracts\Property> $properties
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

    public function filter(PropertyFilter $filter): static
    {
        return new self(
            $this->getStructure(),
            array_filter($this->properties, $filter->check(...))
        );
    }
}