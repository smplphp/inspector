<?php

namespace Smpl\Inspector\Contracts;

/**
 * Property Metadata Collection Contract
 *
 * This contract represents a collection of metadata that belong to a specific
 * property.
 *
 * @see \Smpl\Inspector\Contracts\Metadata
 * @see \Smpl\Inspector\Contracts\MetadataCollection
 */
interface PropertyMetadataCollection extends MetadataCollection
{
    /**
     * Get the property the collection belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Property
     */
    public function getProperty(): Property;
}