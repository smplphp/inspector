<?php

namespace Smpl\Inspector\Contracts;

use Stringable;

/**
 * Attribute Contract
 *
 * This contract represents an available attribute as stand-alone concept. It is
 * a sibling contract to {@see \Smpl\Inspector\Contracts\Metadata} which represents
 * an instance of an attribute, assigned to something.
 */
interface Attribute extends Stringable
{
    /**
     * Get the class name of the attribute.
     *
     * @return class-string
     */
    public function getName(): string;

    /**
     * Check if the attribute is repeatable.
     *
     * @return bool
     */
    public function isRepeatable(): bool;

    /**
     * Get an array of valid targets for this attribute.
     *
     * @return \Smpl\Inspector\Support\AttributeTarget[]
     */
    public function getTargets(): array;
}