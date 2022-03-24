<?php

namespace Smpl\Inspector\Contracts;

/**
 * Structure Property Collection
 *
 * This contract represents a collection of properties that belong to a single
 * structure.
 *
 * @see \Smpl\Inspector\Contracts\Property
 * @see \Smpl\Inspector\Contracts\PropertyCollection
 */
interface StructurePropertyCollection extends PropertyCollection
{
    /**
     * Get the structure these properties belong to.
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getStructure(): Structure;

    /**
     * Turn this collection into its base version.
     *
     * @return \Smpl\Inspector\Contracts\PropertyCollection
     */
    public function asBase(): PropertyCollection;
}