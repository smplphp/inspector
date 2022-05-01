<?php

namespace Smpl\Inspector\Contracts;

use ReflectionFunctionAbstract;

/**
 * Base Function Contract
 *
 * This contract represents a base function, either a method or a closure.
 *
 * @see \Smpl\Inspector\Contracts\Closure
 * @see \Smpl\Inspector\Contracts\Method
 */
interface BaseFunction
{
    /**
     * Get the reflection instance for this function.
     *
     * @return \ReflectionFunctionAbstract
     */
    public function getReflection(): ReflectionFunctionAbstract;

    /**
     * Check if the function is static.
     *
     * @return bool
     */
    public function isStatic(): bool;

    /**
     * Get the return type of the function.
     *
     * @return \Smpl\Inspector\Contracts\Type|null
     */
    public function getReturnType(): ?Type;

    /**
     * Get a collection of parameters for this function.
     *
     * If the $filter parameter is provided the results will be filtered according
     * to the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\ParameterFilter|null $filter
     *
     * @return \Smpl\Inspector\Contracts\ParameterCollection
     *
     * @see \Smpl\Inspector\Contracts\ParameterCollection::filter()
     */
    public function getParameters(?ParameterFilter $filter = null): ParameterCollection;

    /**
     * Get a parameter for this function by name or position.
     *
     * @param string|int $parameter
     *
     * @return \Smpl\Inspector\Contracts\Parameter|null
     *
     * @see \Smpl\Inspector\Contracts\ParameterCollection::get()
     */
    public function getParameter(string|int $parameter): ?Parameter;

    /**
     * Check if this function has a parameter by the provided name or at the
     * provided position.
     *
     * @param string|int $parameter
     *
     * @return bool
     *
     * @see \Smpl\Inspector\Contracts\MethodParameterCollection::has()
     */
    public function hasParameter(string|int $parameter): bool;
}