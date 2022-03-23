<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\StructureType;

/**
 * Structure Filter Contract
 *
 * This contract represents a filter object for filtering a collection of
 * structures.
 *
 * @see \Smpl\Inspector\Contracts\Structure
 * @see \Smpl\Inspector\Contracts\StructureCollection
 * @see \Smpl\Inspector\Contracts\StructureCollection::filter()
 */
interface StructureFilter
{
    /**
     * Filter structures by the provided structure type.
     *
     * @param \Smpl\Inspector\Support\StructureType $type
     *
     * @return static
     */
    public function ofType(StructureType $type): static;

    /**
     * Filter structure by the provided structure types.
     *
     * @param \Smpl\Inspector\Support\StructureType ...$types
     *
     * @return static
     */
    public function ofTypes(StructureType ...$types): static;

    /**
     * Filter structures that are part of the provided namespace.
     *
     * @param string $namespace
     *
     * @return static
     */
    public function inNamespace(string $namespace): static;

    /**
     * Filter structures that are not part of the provided namespace.
     *
     * @param string $namespace
     *
     * @return static
     */
    public function notInNamespace(string $namespace): static;

    /**
     * Filter structures that have constructors.
     *
     * @return static
     */
    public function hasConstructor(): static;

    /**
     * Filter structures that do not have constructors.
     *
     * @return static
     */
    public function hasNoConstructor(): static;

    /**
     * Filter structures that are instantiable.
     *
     * @return static
     */
    public function isInstantiable(): static;

    /**
     * Filter structures that are not instantiable.
     *
     * @return static
     */
    public function isNotInstantiable(): static;

    /**
     * Filter structures that are children of the provided class.
     *
     * @param class-string $class
     *
     * @return static
     */
    public function childOf(string $class): static;

    /**
     * Filter structures that are not children of the provided class.
     *
     * @param class-string $class
     *
     * @return static
     */
    public function notChildOf(string $class): static;

    /**
     * Filter structures that are accepted by the provided type.
     *
     * @param \Smpl\Inspector\Contracts\Type $type
     *
     * @return static
     */
    public function acceptedBy(Type $type): static;

    /**
     * Filter structrues that are not accepted by the provided type.
     *
     * @param \Smpl\Inspector\Contracts\Type $type
     *
     * @return static
     */
    public function notAcceptedBy(Type $type): static;

    /**
     * Filter structures that implement the provided interface.
     *
     * @param class-string $interface
     *
     * @return static
     */
    public function implements(string $interface): static;

    /**
     * Filter structures that do not implement the provided interface.
     *
     * @param class-string $interface
     *
     * @return static
     */
    public function doesNotImplement(string $interface): static;

    /**
     * Filter structures that use the provided trait.
     *
     * @param trait-string $trait
     *
     * @return static
     */
    public function uses(string $trait): static;

    /**
     * Filter structures that do not use the provided trait.
     *
     * @param trait-string $trait
     *
     * @return static
     */
    public function doesNoUse(string $trait): static;

    /**
     * Filter structures that have methods that match the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\MethodFilter $filter
     *
     * @return static
     */
    public function methodsMatch(MethodFilter $filter): static;

    /**
     * Filter structures that have properties that match the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\PropertyFilter $filter
     *
     * @return static
     */
    public function propertiesMatch(PropertyFilter $filter): static;

    /**
     * Filter structures that have the provided attribute.
     *
     * @param class-string $attribute
     * @param bool         $instanceOf If true, an instanceof check will be used,
     *                                 otherwise, it will use an exact match.
     *
     * @return static
     */
    public function hasAttribute(string $attribute, bool $instanceOf = false): static;

    /**
     * Check a structure against the filters.
     *
     * @param \Smpl\Inspector\Contracts\Structure $structure
     *
     * @return bool
     */
    public function check(Structure $structure): bool;
}