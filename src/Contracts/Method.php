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
interface Method extends AttributableElement
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
     * Get the full name of the method, including the class name.
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
     * Check if the method is static.
     *
     * @return bool
     */
    public function isStatic(): bool;

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
     * Get the return type of the method.
     *
     * @return \Smpl\Inspector\Contracts\Type|null
     */
    public function getReturnType(): ?Type;

    /**
     * Get the structure this method belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getStructure(): Structure;

    /**
     * Get a collection of parameters for this method.
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
     * Get a parameter for this method by name or position.
     *
     * @param string|int $parameter
     *
     * @return \Smpl\Inspector\Contracts\Parameter|null
     *
     * @see \Smpl\Inspector\Contracts\MethodParameterCollection::get()
     */
    public function getParameter(string|int $parameter): ?Parameter;

    /**
     * Check if this method has a parameter by the provided name or at the
     * provided position.
     *
     * @param string|int $parameter
     *
     * @return bool
     *
     * @see \Smpl\Inspector\Contracts\MethodParameterCollection::has()
     */
    public function hasParameter(string|int $parameter): bool;

    /**
     * Get a collection of metadata for this method.
     *
     * @return \Smpl\Inspector\Contracts\MethodMetadataCollection
     */
    public function getAllMetadata(): MethodMetadataCollection;
}