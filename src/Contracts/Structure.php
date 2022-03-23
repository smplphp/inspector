<?php

namespace Smpl\Inspector\Contracts;

use ReflectionClass;
use Smpl\Inspector\Support\StructureType;

/**
 * Structure Contract
 *
 * This contract represents PHP structures, such as, Classes, Interfaces, Traits,
 * Enums and Attributes.
 */
interface Structure extends AttributableElement
{
    /**
     * The separator used between a structure name and its
     * method/property.
     */
    public const SEPARATOR = '::';

    /**
     * Get the reflection instance for this structure.
     *
     * @return \ReflectionClass
     */
    public function getReflection(): ReflectionClass;

    /**
     * Get the type of this structure.
     *
     * The type returned by this method is an instance of
     * {@see \Smpl\Inspector\Contracts\Type} used for type comparison.
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function getType(): Type;

    /**
     * Get the structure type for this structure.
     *
     * The type returned by this method is an instance of the enum
     * {@see \Smpl\Inspector\Support\StructureType} and represents whether
     * the structure is a class, interface, trait, enum or attribute.
     *
     * @return \Smpl\Inspector\Support\StructureType
     */
    public function getStructureType(): StructureType;

    /**
     * Get the name of the structure without its namespace.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the name of the structure with its namespace.
     *
     * @return class-string
     */
    public function getFullName(): string;

    /**
     * Get the namespace of this structure.
     *
     * @return string
     */
    public function getNamespace(): string;

    /**
     * Whether the structure is instantiable.
     *
     * @return bool
     */
    public function isInstantiable(): bool;

    /**
     * Get the parent of this structure.
     *
     * @return \Smpl\Inspector\Contracts\Structure|null
     */
    public function getParent(): ?Structure;

    /**
     * Get a collection of properties belonging to this structure.
     *
     * @return \Smpl\Inspector\Contracts\StructurePropertyCollection
     */
    public function getProperties(): StructurePropertyCollection;

    /**
     * Get a single property belonging to this structure.
     *
     * @param string $name
     *
     * @return \Smpl\Inspector\Contracts\Property|null
     */
    public function getProperty(string $name): ?Property;

    /**
     * Check if this structure has a property by the provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty(string $name): bool;

    /**
     * Get a collection of methods belonging to this structure.
     *
     * @return \Smpl\Inspector\Contracts\StructureMethodCollection
     */
    public function getMethods(): StructureMethodCollection;

    /**
     * Get the structures' constructor, if there is one.
     *
     * @return \Smpl\Inspector\Contracts\Method|null
     */
    public function getConstructor(): ?Method;

    /**
     * Get a single method belonging to this structure.
     *
     * @param string $name
     *
     * @return \Smpl\Inspector\Contracts\Method|null
     */
    public function getMethod(string $name): ?Method;

    /**
     * Check if this structure has a method by the provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasMethod(string $name): bool;

    /**
     * Get a collection of metadata on this structure.
     *
     * @return \Smpl\Inspector\Contracts\StructureMetadataCollection
     *
     * @see \Smpl\Inspector\Contracts\Metadata
     */
    public function getAllMetadata(): StructureMetadataCollection;

    /**
     * Get a collection of structures representing the interfaces this structure
     * implements.
     *
     * @return \Smpl\Inspector\Contracts\StructureCollection
     */
    public function getInterfaces(): StructureCollection;

    /**
     * Check if this structure implements the provided interface.
     *
     * @param class-string|\Smpl\Inspector\Contracts\Structure $interface
     *
     * @return bool
     */
    public function implements(string|Structure $interface): bool;

    /**
     * Get a collection of structures representing the traits this structure
     * uses.
     *
     * @return \Smpl\Inspector\Contracts\StructureCollection
     */
    public function getTraits(): StructureCollection;

    /**
     * Check if this structure uses the provided trait.
     *
     * @param class-string|\Smpl\Inspector\Contracts\Structure $trait
     *
     * @return bool
     */
    public function uses(string|Structure $trait): bool;
}