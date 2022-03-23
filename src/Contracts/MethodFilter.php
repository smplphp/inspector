<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\Visibility;

/**
 * Method Filter Contract
 *
 * This represents a filter object used to filter a collection of methods.
 *
 * @see \Smpl\Inspector\Contracts\Method
 * @see \Smpl\Inspector\Contracts\MethodCollection
 * @see \Smpl\Inspector\Contracts\MethodCollection::filter()
 */
interface MethodFilter
{
    /**
     * Filter only public methods.
     *
     * @return static
     */
    public function publicOnly(): static;

    /**
     * Filter only protected methods.
     *
     * @return static
     */
    public function protectedOnly(): static;

    /**
     * Filter only private methods.
     *
     * @return static
     */
    public function privateOnly(): static;

    /**
     * Filter methods using the provided visibilities.
     *
     * @param \Smpl\Inspector\Support\Visibility ...$visibilities
     *
     * @return static
     */
    public function hasVisibility(Visibility ...$visibilities): static;

    /**
     * Filter methods by their return type.
     *
     * If $type is null, methods will be filtered by return type presence,
     * otherwise, the value of $type will be used to only return methods
     * that have the provided type.
     *
     * @param string|\Smpl\Inspector\Contracts\Type|null $type
     *
     * @return static
     */
    public function hasReturnType(string|Type|null $type = null): static;

    /**
     * Filter methods by their lack of return type.
     *
     * @return static
     */
    public function hasNoReturnType(): static;

    /**
     * Filter methods that are static.
     *
     * @return static
     */
    public function static(): static;

    /**
     * Filter methods that are not static.
     *
     * @return static
     */
    public function notStatic(): static;

    /**
     * Filter methods without parameters.
     *
     * @return static
     */
    public function hasNoParameters(): static;

    /**
     * Filter methods with parameters.
     *
     * @return static
     */
    public function hasParameters(): static;

    /**
     * Filter methods with the provided number of parameters.
     *
     * @param int $parameterCount
     *
     * @return static
     */
    public function parameterCount(int $parameterCount): static;

    /**
     * Filter methods that have the provided attribute.
     *
     * @param class-string $attribute
     * @param bool         $instanceOf If true, an instanceof check will be used,
     *                                 otherwise, it will use an exact match.
     *
     * @return static
     */
    public function hasAttribute(string $attribute, bool $instanceOf = false): static;

    /**
     * Filter methods by their parameters.
     *
     * @param \Smpl\Inspector\Contracts\ParameterFilter $filter
     *
     * @return static
     */
    public function parametersMatch(ParameterFilter $filter): static;

    /**
     * Check a method against the filters.
     *
     * @param \Smpl\Inspector\Contracts\Method $method
     *
     * @return bool
     */
    public function check(Method $method): bool;
}