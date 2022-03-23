<?php

namespace Smpl\Inspector\Contracts;

/**
 * Method Parameter Collection Contract
 *
 * This contract represents a collection of parameters that belong to a specific
 * method.
 *
 * @see \Smpl\Inspector\Contracts\Parameter
 * @see \Smpl\Inspector\Contracts\ParameterCollection
 */
interface MethodParameterCollection extends ParameterCollection
{
    /**
     * Get the method the collection belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Method
     */
    public function getMethod(): Method;
}