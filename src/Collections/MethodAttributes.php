<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\MethodAttributeCollection;
use Smpl\Inspector\Support\AttributeTarget;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Collections\Attributes<I>
 * @implements \Smpl\Inspector\Contracts\MethodAttributeCollection<I>
 */
final class MethodAttributes extends Attributes implements MethodAttributeCollection
{
    private Method $method;

    /**
     * @param \Smpl\Inspector\Contracts\Method                                     $method
     * @param list<\Smpl\Inspector\Contracts\Attribute>                            $attributes
     * @param array<class-string, \Smpl\Inspector\Contracts\MetadataCollection<I>> $metadata
     */
    public function __construct(Method $method, array $attributes, array $metadata)
    {
        parent::__construct($attributes, $metadata);
        $this->method = $method;
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function getAttributeTarget(): AttributeTarget
    {
        return AttributeTarget::Method;
    }
}