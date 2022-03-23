<?php

declare(strict_types=1);

namespace Smpl\Inspector\Contracts;

/**
 * Attributable Element Contract
 *
 * This contract is designed to be used alongside other contracts that represent
 * elements that have attributes, such as classes, methods, properties and parameters.
 *
 * @see \Smpl\Inspector\Contracts\MetadataCollection
 */
interface AttributableElement
{
    /**
     * Check if this element has the provided attribute attached.
     *
     * @param class-string $attributeClass
     * @param bool         $instanceOf If true, an instanceof check will be used,
     *                                 otherwise, it will use an exact match.
     *
     * @return bool
     *
     * @see \Smpl\Inspector\Contracts\MetadataCollection::has()
     */
    public function hasAttribute(string $attributeClass, bool $instanceOf = false): bool;

    /**
     * Get the first piece of metadata that this element has for the provided
     * attribute class.
     *
     * @param class-string $attributeClass
     * @param bool         $instanceOf If true, an instanceof check will be used,
     *                                 otherwise, it will use an exact match.
     *
     * @return \Smpl\Inspector\Contracts\Metadata|null
     *
     * @see \Smpl\Inspector\Contracts\MetadataCollection::first()
     */
    public function getFirstMetadata(string $attributeClass, bool $instanceOf = false): ?Metadata;

    /**
     * Get all metadata that this element has for the provided attribute class.
     *
     * @param class-string $attributeClass
     * @param bool         $instanceOf If true, an instanceof check will be used,
     *                                 otherwise, it will use an exact match.
     *
     * @return \Smpl\Inspector\Contracts\Metadata[]
     *
     * @see \Smpl\Inspector\Contracts\MetadataCollection::get()
     */
    public function getMetadata(string $attributeClass, bool $instanceOf = false): array;

    /**
     * Get an array of attributes used by this class.
     *
     * @return list<\Smpl\Inspector\Contracts\Attribute>
     *
     * @see \Smpl\Inspector\Contracts\MetadataCollection::getAttributes()
     */
    public function getAttributes(): array;
}