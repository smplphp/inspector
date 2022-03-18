<?php

namespace Smpl\Inspector\Contracts;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Contracts\AttributeCollection<I>
 */
interface PropertyAttributeCollection extends AttributeCollection
{
    public function getProperty(): Property;
}