<?php

namespace Smpl\Inspector\Contracts;

use ReflectionMethod;
use Smpl\Inspector\Support\Visibility;

/**
 * Method Contract
 *
 * This contract represents a method within a PHP structure.
 *
 * @see \Smpl\Inspector\Contracts\Structure
 */
interface Method extends BaseFunction, AttributableElement
{
    /**
     * Get the reflection instance for this method.
     *
     * @return \ReflectionMethod
     */
    public function getReflection(): ReflectionMethod;

    /**
     * Get the name of the method.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the full name of the method.
     *
     * @return string
     */
    public function getFullName(): string;

    /**
     * Get the visibility of the method.
     *
     * @return \Smpl\Inspector\Support\Visibility
     */
    public function getVisibility(): Visibility;

    /**
     * Check if the method is abstract.
     *
     * @return bool
     */
    public function isAbstract(): bool;

    /**
     * Check if the method is a constructor.
     *
     * @return bool
     */
    public function isConstructor(): bool;

    /**
     * Get the structure this method belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getStructure(): Structure;

    /**
     * Get the declaring structure this method belongs to.
     *
     * This method should always return a value, even if it's the same as
     * {@see \Smpl\Inspector\Contracts\Method::getStructure}.
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getDeclaringStructure(): Structure;

    /**
     * Check whether this method is inherited.
     *
     * @return bool
     */
    public function isInherited(): bool;

    /**
     * Get a collection of parameters for this function.
     *
     * If the $filter parameter is provided the results will be filtered according
     * to the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\ParameterFilter|null $filter
     *
     * @return \Smpl\Inspector\Contracts\MethodParameterCollection
     *
     * @see \Smpl\Inspector\Contracts\MethodParameterCollection::filter()
     */
    public function getParameters(?ParameterFilter $filter = null): MethodParameterCollection;

    /**
     * Get a collection of metadata for this method.
     *
     * @return \Smpl\Inspector\Contracts\MethodMetadataCollection
     */
    public function getAllMetadata(): MethodMetadataCollection;
}