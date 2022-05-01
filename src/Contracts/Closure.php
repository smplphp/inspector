<?php

namespace Smpl\Inspector\Contracts;

use ReflectionFunction;

/**
 * Closure Contract
 *
 * This contract represents a closure/anonymous function.
 *
 * @see \Smpl\Inspector\Contracts\Structure
 */
interface Closure extends BaseFunction
{
    /**
     * Get the reflection instance for this closure.
     *
     * @return \ReflectionFunction
     */
    public function getReflection(): ReflectionFunction;

    /**
     * Get a collection of parameters for this closure.
     *
     * If the $filter parameter is provided the results will be filtered according
     * to the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\ParameterFilter|null $filter
     *
     * @return \Smpl\Inspector\Contracts\ClosureParameterCollection
     *
     * @see \Smpl\Inspector\Contracts\ClosureParameterCollection::filter()
     */
    public function getParameters(?ParameterFilter $filter = null): ClosureParameterCollection;
}