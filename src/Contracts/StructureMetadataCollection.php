<?php

namespace Smpl\Inspector\Contracts;

/**
 * Structure Metadata Collection Contract
 *
 * This contract represents a collection of metadata that belong to a specific
 * structure.
 *
 * @see \Smpl\Inspector\Contracts\Metadata
 * @see \Smpl\Inspector\Contracts\MetadataCollection
 */
interface StructureMetadataCollection extends MetadataCollection
{
    /**
     * Get the strcuture the collection belongs to.
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getStructure(): Structure;
}