<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureAttributeCollection;
use Smpl\Inspector\Support\AttributeTarget;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Collections\Attributes<I>
 * @implements \Smpl\Inspector\Contracts\StructureAttributeCollection<I>
 */
final class StructureAttributes extends Attributes implements StructureAttributeCollection
{
    private Structure $structure;

    /**
     * @param \Smpl\Inspector\Contracts\Structure                                  $structure
     * @param list<\Smpl\Inspector\Contracts\Attribute>                            $attributes
     * @param array<class-string, \Smpl\Inspector\Contracts\MetadataCollection<I>> $metadata
     */
    public function __construct(Structure $structure, array $attributes, array $metadata)
    {
        parent::__construct($attributes, $metadata);
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