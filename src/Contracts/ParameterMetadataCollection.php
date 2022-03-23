<?php

namespace Smpl\Inspector\Contracts;

/**
 * Parameter Metadata Collection Contract
 *
 * This contract represents a collection of metadata that belong to a specific
 * parameter.
 *
 * @see \Smpl\Inspector\Contracts\Metadata
 * @see \Smpl\Inspector\Contracts\MetadataCollection
 */
interface ParameterMetadataCollection extends MetadataCollection
{
    /**
     * Get the parameter the collection belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Parameter
     */
    public function getParameter(): Parameter;
}