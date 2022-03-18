<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\ParameterAttributeCollection;
use Smpl\Inspector\Support\AttributeTarget;

/**
 * @template I of object
 *
 * @extends \Smpl\Inspector\Collections\Attributes<I>
 * @implements \Smpl\Inspector\Contracts\ParameterAttributeCollection<I>
 */
final class ParameterAttributes extends Attributes implements ParameterAttributeCollection
{
    private Parameter $parameter;

    /**
     * @param array<class-string, \Smpl\Inspector\Contracts\Attribute>             $attributes
     * @param array<class-string, \Smpl\Inspector\Contracts\MetadataCollection<I>> $metadata
     */
    public function __construct(Parameter $parameter, array $attributes, array $metadata)
    {
        parent::__construct($attributes, $metadata);
        $this->parameter = $parameter;
    }

    public function getParameter(): Parameter
    {
        return $this->parameter;
    }

    public function getAttributeTarget(): AttributeTarget
    {
        return AttributeTarget::Parameter;
    }
}