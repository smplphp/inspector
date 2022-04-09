<?php

namespace Smpl\Inspector\Contracts;

/**
 * Wrapper Type Contract
 *
 * This contract represents a type that wraps another type, essentially for the
 * purpose of proxying.
 */
interface WrapperType
{
    /**
     * Get the wrapped type.
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function getBaseType(): Type;
}