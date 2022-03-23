<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\AttributeTarget;

/**
 * Metadata Collection Contract
 *
 * This contract represents a collection of metadata and their associative
 * attributes.
 *
 * @extends \Smpl\Inspector\Contracts\Collection<int, \Smpl\Inspector\Contracts\Metadata>
 */
interface MetadataCollection extends Collection
{
    /**
     * Get all attributes this collection represents.
     *
     * @return list<\Smpl\Inspector\Contracts\Attribute>
     */
    public function getAttributes(): array;

    /**
     * Get an attribute by its class.
     *
     * @param class-string $attributeName
     *
     * @return \Smpl\Inspector\Contracts\Attribute|null
     */
    public function getAttribute(string $attributeName): ?Attribute;

    /**
     * Get all metadata for the provided attribute.
     *
     * @param class-string|\Smpl\Inspector\Contracts\Attribute $attribute
     * @param bool                                             $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata[]
     */
    public function get(string|Attribute $attribute, bool $instanceOf = false): array;

    /**
     * Get the first piece of metadata for the provided attribute.
     *
     * @param class-string|\Smpl\Inspector\Contracts\Attribute|null $attribute
     * @param bool                                                  $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata|null
     */
    public function first(string|Attribute|null $attribute = null, bool $instanceOf = false): ?Metadata;

    /**
     * Check if the collection contains metadata for the provided attribute.
     *
     * @param class-string|\Smpl\Inspector\Contracts\Attribute $attribute
     * @param bool                                             $instanceOf
     *
     * @return bool
     */
    public function has(string|Attribute $attribute, bool $instanceOf = false): bool;

    /**
     * Get the number of metadata instances for the provided attribute.
     *
     * @param class-string|\Smpl\Inspector\Contracts\Attribute $attribute
     * @param bool                                             $instanceOf
     *
     * @return int
     */
    public function countInstances(string|Attribute $attribute, bool $instanceOf = false): int;

    /**
     * Get the target for this collection.
     *
     * @return \Smpl\Inspector\Support\AttributeTarget
     */
    public function getAttributeTarget(): AttributeTarget;
}