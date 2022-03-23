<?php

namespace Smpl\Inspector\Contracts;

/**
 * Parameter Filter Contract
 *
 * This represents a filter object used to filter parameter collections.
 *
 * @see \Smpl\Inspector\Contracts\Property
 * @see \Smpl\Inspector\Contracts\ParameterCollection
 * @see \Smpl\Inspector\Contracts\ParameterCollection::filter()
 */
interface ParameterFilter
{
    /**
     * Filter parameters that have types.
     *
     * @return static
     */
    public function typed(): static;

    /**
     * Filter parameters that do not have types.
     *
     * @return static
     */
    public function notTyped(): static;

    /**
     * Filter parameters that are promoted.
     *
     * @return static
     */
    public function promoted(): static;

    /**
     * Filter parameters that are not promoted.
     *
     * @return static
     */
    public function notPromoted(): static;

    /**
     * Filter parameters that are variadic.
     *
     * @return static
     */
    public function variadic(): static;

    /**
     * Filter parameters that are not variadic.
     *
     * @return static
     */
    public function notVariadic(): static;

    /**
     * Filter methods by their type.
     *
     * @param string|\Smpl\Inspector\Contracts\Type $type
     *
     * @return static
     */
    public function hasType(string|Type $type): static;

    /**
     * Filter parameters that are nullable.
     *
     * @return static
     */
    public function nullable(): static;

    /**
     * Filter parameters that are not nullable.
     *
     * @return static
     */
    public function notNullable(): static;

    /**
     * Filter parameters that have a default value.
     *
     * @return static
     */
    public function hasDefaultValue(): static;

    /**
     * Filter parameters that do not have a default value.
     *
     * @return static
     */
    public function noDefaultValue(): static;

    /**
     * Filter parameters that have the provided attribute.
     *
     * @param class-string $attribute
     * @param bool         $instanceOf If true, an instanceof check will be used,
     *                                 otherwise, it will use an exact match.
     *
     * @return static
     */
    public function hasAttribute(string $attribute, bool $instanceOf = false): static;

    /**
     * Check a parameter against the filters.
     *
     * @param \Smpl\Inspector\Contracts\Parameter $parameter
     *
     * @return bool
     */
    public function check(Parameter $parameter): bool;
}