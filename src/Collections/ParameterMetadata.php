<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\ParameterMetadataCollection;
use Smpl\Inspector\Support\AttributeTarget;

final class ParameterMetadata extends MetadataCollection implements ParameterMetadataCollection
{
    private Parameter $parameter;

    /**
     * @param \Smpl\Inspector\Contracts\Parameter      $parameter
     * @param list<\Smpl\Inspector\Contracts\Metadata> $metadata
     */
    public function __construct(Parameter $parameter, array $metadata)
    {
        parent::__construct($metadata);
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