<?php

namespace Smpl\Inspector\Contracts;

use ReflectionAttribute;

/**
 * Metadata Contract
 *
 * This contract represents an instance of {@see \Smpl\Inspector\Contracts\Attribute}
 * applied to target.
 *
 * @template I of object
 */
interface Metadata
{
    /**
     * Get the attribute this metadata represents.
     *
     * @return \Smpl\Inspector\Contracts\Attribute
     */
    public function getAttribute(): Attribute;

    /**
     * Get the reflection instance for this metadata.
     *
     * @return \ReflectionAttribute<I>
     */
    public function getReflection(): ReflectionAttribute;

    /**
     * Get an instance of the underlying metadata, including all arguments.
     *
     * @return I
     */
    public function getInstance(): object;
}