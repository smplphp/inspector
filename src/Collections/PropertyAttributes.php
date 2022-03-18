<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyAttributeCollection;
use Smpl\Inspector\Support\AttributeTarget;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Collections\Attributes<I>
 * @implements \Smpl\Inspector\Contracts\PropertyAttributeCollection<I>
 */
final class PropertyAttributes extends Attributes implements PropertyAttributeCollection
{
    private Property $property;

    /**
     * @param list<\Smpl\Inspector\Contracts\Attribute>                         $attributes
     * @param array<class-string, \Smpl\Inspector\Contracts\MetadataCollection<I>> $metadata
     */
    public function __construct(Property $property, array $attributes, array $metadata)
    {
        parent::__construct($attributes, $metadata);
        $this->property = $property;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getAttributeTarget(): AttributeTarget
    {
        return AttributeTarget::Property;
    }
}