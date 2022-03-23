<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureMetadataCollection;
use Smpl\Inspector\Support\AttributeTarget;

final class StructureMetadata extends MetadataCollection implements StructureMetadataCollection
{
    private Structure $structure;

    /**
     * @param \Smpl\Inspector\Contracts\Structure      $structure
     * @param list<\Smpl\Inspector\Contracts\Metadata> $metadata
     */
    public function __construct(Structure $structure, array $metadata)
    {
        parent::__construct($metadata);
        $this->structure = $structure;
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function getAttributeTarget(): AttributeTarget
    {
        return AttributeTarget::Structure;
    }
}