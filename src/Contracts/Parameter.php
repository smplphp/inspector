<?php

declare(strict_types=1);

namespace Smpl\Inspector\Contracts;

use ReflectionParameter;

/**
 * Parameter Contract
 *
 * This contract represents a method parameter.
 *
 * @see \Smpl\Inspector\Contracts\Method
 * @see \Smpl\Inspector\Contracts\ParameterCollection
 */
interface Parameter extends AttributableElement
{
    /**
     * Get the reflection instance for the parameter.
     *
     * @return \ReflectionParameter
     */
    public function getReflection(): ReflectionParameter;

    /**
     * Get the method this parameter belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Method
     */
    public function getMethod(): Method;

    /**
     * Get the property this parameter is promoted to, if it is promoted.
     *
     * @return \Smpl\Inspector\Contracts\Property|null
     */
    public function getProperty(): ?Property;

    /**
     * Get the name of this parameter.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the position of this parameter.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Get the parameter type, if there is one.
     *
     * @return \Smpl\Inspector\Contracts\Type|null
     */
    public function getType(): ?Type;

    /**
     * Check if the parameter is nullable.
     *
     * @return bool
     */
    public function isNullable(): bool;

    /**
     * Check if the parameter has a default value.
     *
     * @return bool
     */
    public function hasDefault(): bool;

    /**
     * Get the parameters default value.
     *
     * @return mixed
     */
    public function getDefault(): mixed;

    /**
     * Check if the parameter is variadic.
     *
     * @return bool
     */
    public function isVariadic(): bool;

    /**
     * Check if the parameter is promoted.
     *
     * @return bool
     */
    public function isPromoted(): bool;

    /**
     * Get a collection of metadata attached to this parameter.
     *
     * @return \Smpl\Inspector\Contracts\ParameterMetadataCollection
     */
    public function getAllMetadata(): ParameterMetadataCollection;
}