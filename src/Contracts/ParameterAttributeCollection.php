<?php

namespace Smpl\Inspector\Contracts;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Contracts\AttributeCollection<I>
 */
interface ParameterAttributeCollection extends AttributeCollection
{
    public function getParameter(): Parameter;
}