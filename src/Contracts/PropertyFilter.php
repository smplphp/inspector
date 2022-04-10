<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\Visibility;

/**
 * Property Filter Contract
 *
 * This contract represents a filter object used to filter a collection of
 * properties.
 *
 * @see \Smpl\Inspector\Contracts\Property
 * @see \Smpl\Inspector\Contracts\PropertyCollection
 * @see \Smpl\Inspector\Contracts\PropertyCollection::filter()
 */
interface PropertyFilter
{
    /**
     * Filter only public properties.
     *
     * @return static
     */
    public function publicOnly(): static;

    /**
     * Filter only protected properties.
     *
     * @return static
     */
    public function protectedOnly(): static;

    /**
     * Filter only private properties.
     *
     * @return static
     */
    public function privateOnly(): static;

    /**
     * Filter properties using the provided visibilities.
     *
     * @param \Smpl\Inspector\Support\Visibility ...$visibilities
     *
     * @return static
     */
    public function hasVisibility(Visibility ...$visibilities): static;

    /**
     * Filter properties that have types.
     *
     * @return static
     */
    public function typed(): static;

    /**
     * Filter properties that do not have types.
     *
     * @return static
     */
    public function notTyped(): static;

    /**
     * Filter properties by their type.
     *
     * @param string|\Smpl\Inspector\Contracts\Type $type
     *
     * @return static
     */
    public function hasType(string|Type $type): static;

    /**
     * Filter properties by types that accept the provided type.
     *
     * @param string|\Smpl\Inspector\Contracts\Type $type
     *
     * @return static
     */
    public function typeAccepts(string|Type $type): static;

    /**
     * Filter properties by types that do not accept the provided type.
     *
     * @param string|\Smpl\Inspector\Contracts\Type $type
     *
     * @return static
     */
    public function typeDoesNotAccept(string|Type $type): static;

    /**
     * Filter properties by types that match the provided value.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function typeMatches(mixed $value): static;

    /**
     * Filter properties by types that do not match the provided value.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function typeDoesNotMatch(mixed $value): static;

    /**
     * Filter properties that are static.
     *
     * @return static
     */
    public function static(): static;

    /**
     * Filter properties that are not static.
     *
     * @return static
     */
    public function notStatic(): static;

    /**
     * Filter properties that are nullable.
     *
     * @return static
     */
    public function nullable(): static;

    /**
     * Filter properties that are not nullable.
     *
     * @return static
     */
    public function notNullable(): static;

    /**
     * Filter properties that have a default value.
     *
     * @return static
     */
    public function hasDefaultValue(): static;

    /**
     * Filter properties that not have a default value.
     *
     * @return static
     */
    public function noDefaultValue(): static;

    /**
     * Filter properties that have the provided attribute.
     *
     * @param class-string $attribute
     * @param bool         $instanceOf If true, an instanceof check will be used,
     *                                 otherwise, it will use an exact match.
     *
     * @return static
     */
    public function hasAttribute(string $attribute, bool $instanceOf = false): static;

    /**
     * Check a property against the filters.
     *
     * @param \Smpl\Inspector\Contracts\Property $property
     *
     * @return bool
     */
    public function check(Property $property): bool;
}