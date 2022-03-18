<?php

namespace Smpl\Inspector\Contracts;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Contracts\AttributeCollection<I>
 */
interface StructureAttributeCollection extends AttributeCollection
{
    public function getStructure(): Structure;
}