<?php

namespace Smpl\Inspector\Contracts;

use ReflectionAttribute;

/**
 * @template I of object
 */
interface Metadata
{
    public function getAttribute(): Attribute;

    public function getReflection(): ReflectionAttribute;

    /**
     * @return I
     */
    public function getInstance(): object;
}