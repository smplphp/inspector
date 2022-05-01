<?php

namespace Smpl\Inspector\Contracts;

/**
 * Closure Parameter Collection Contract
 *
 * This contract represents a collection of parameters that belong to a specific
 * closure.
 *
 * @see \Smpl\Inspector\Contracts\Parameter
 * @see \Smpl\Inspector\Contracts\ParameterCollection
 */
interface ClosureParameterCollection extends ParameterCollection
{
    /**
     * Get the closure the collection belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Closure
     */
    public function getClosure(): Closure;

    /**
     * Turn this collection into its base version.
     *
     * @return \Smpl\Inspector\Contracts\ParameterCollection
     */
    public function asBase(): ParameterCollection;
}