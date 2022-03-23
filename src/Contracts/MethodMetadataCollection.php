<?php

namespace Smpl\Inspector\Contracts;

/**
 * Method Metadata Collection Contract
 *
 * This contract represents a collection of metadata that belong to a specific
 * method.
 *
 * @see \Smpl\Inspector\Contracts\Metadata
 * @see \Smpl\Inspector\Contracts\MetadataCollection
 */
interface MethodMetadataCollection extends MetadataCollection
{
    /**
     * Get the method the collection belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Method
     */
    public function getMethod(): Method;
}