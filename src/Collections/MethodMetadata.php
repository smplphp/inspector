<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\MethodMetadataCollection;
use Smpl\Inspector\Support\AttributeTarget;

final class MethodMetadata extends MetadataCollection implements MethodMetadataCollection
{
    private Method $method;

    /**
     * @param \Smpl\Inspector\Contracts\Method    $method
     * @param \Smpl\Inspector\Contracts\Metadata[] $metadata
     */
    public function __construct(Method $method, array $metadata)
    {
        parent::__construct($metadata);
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