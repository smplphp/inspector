<?php

namespace Smpl\Inspector\Contracts;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Contracts\AttributeCollection<I>
 */
interface MethodAttributeCollection extends AttributeCollection
{
    public function getMethod(): Method;
}