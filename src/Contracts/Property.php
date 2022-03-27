<?php

namespace Smpl\Inspector\Contracts;

use ReflectionProperty;
use Smpl\Inspector\Support\Visibility;

/**
 * Property Contract
 *
 * This contract represents a property belonging to a structure.
 *
 * @see \Smpl\Inspector\Contracts\Structure
 */
interface Property extends AttributableElement
{
    /**
     * Get the reflection instance for this property.
     *
     * @return \ReflectionProperty
     */
    public function getReflection(): ReflectionProperty;

    /**
     * Get the structure this property belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getStructure(): Structure;

    /**
     * Get the declaring structure this property belongs to.
     *
     * This method should always return a value, even if it's the same as
     * {@see \Smpl\Inspector\Contracts\Property::getStructure}.
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getDeclaringStructure(): Structure;

    /**
     * Check whether this property is inherited.
     *
     * @return bool
     */
    public function isInherited(): bool;

    /**
     * Get the name of the property.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the full name of the property, including the class name.
     *
     * @return string
     */
    public function getFullName(): string;

    /**
     * Get the property type, if there is one.
     *
     * @return \Smpl\Inspector\Contracts\Type|null
     */
    public function getType(): ?Type;

    /**
     * Get the visibility of the property.
     *
     * @return \Smpl\Inspector\Support\Visibility
     */
    public function getVisibility(): Visibility;

    /**
     * Check if the property is static.
     *
     * @return bool
     */
    public function isStatic(): bool;

    /**
     * Check if the property is nullable.
     *
     * @return bool
     */
    public function isNullable(): bool;

    /**
     * Check if the property has a default value.
     *
     * @return bool
     */
    public function hasDefault(): bool;

    /**
     * Get the default value of the property, if there is one.
     *
     * @return mixed
     */
    public function getDefault(): mixed;

    /**
     * Check if the property is promoted.
     *
     * @return bool
     */
    public function isPromoted(): bool;

    /**
     * Get a collection of metadata for this property.
     *
     * @return \Smpl\Inspector\Contracts\PropertyMetadataCollection
     */
    public function getAllMetadata(): PropertyMetadataCollection;
}