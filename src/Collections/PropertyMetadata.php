<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyMetadataCollection;
use Smpl\Inspector\Support\AttributeTarget;

final class PropertyMetadata extends MetadataCollection implements PropertyMetadataCollection
{
    private Property $property;

    /**
     * @param list<\Smpl\Inspector\Contracts\Metadata> $metadata
     */
    public function __construct(Property $property, array $metadata)
    {
        parent::__construct($metadata);
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