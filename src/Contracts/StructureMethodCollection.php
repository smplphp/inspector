<?php

namespace Smpl\Inspector\Contracts;

/**
 *  Structure Method Collection
 *
 * This contract represents a collection of methods that belong to a specific
 * structure.
 *
 * @see \Smpl\Inspector\Contracts\Method
 * @see \Smpl\Inspector\Contracts\MethodCollection
 */
interface StructureMethodCollection extends MethodCollection
{
    /**
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function getStructure(): Structure;
}