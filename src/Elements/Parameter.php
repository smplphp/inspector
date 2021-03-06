<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionParameter;
use Smpl\Inspector\Concerns\HasAttributes;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Parameter as ParameterContract;
use Smpl\Inspector\Contracts\ParameterMetadataCollection;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\StructureFactory;

class Parameter implements ParameterContract
{
    use HasAttributes;

    private Method                      $method;
    private ?Property                   $property;
    private ReflectionParameter         $reflection;
    private ?Type                       $type;
    private ParameterMetadataCollection $metadata;

    public function __construct(Method $method, ReflectionParameter $reflection, ?Type $type = null)
    {
        $this->method     = $method;
        $this->reflection = $reflection;
        $this->type       = $type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getReflection(): ReflectionParameter
    {
        return $this->reflection;
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function getName(): string
    {
        return $this->getReflection()->getName();
    }

    public function getPosition(): int
    {
        return $this->getReflection()->getPosition();
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->getReflection()->allowsNull();
    }

    public function hasDefault(): bool
    {
        return $this->getReflection()->isDefaultValueAvailable();
    }

    public function getDefault(): mixed
    {
        if ($this->hasDefault()) {
            return $this->getReflection()->getDefaultValue();
        }

        return null;
    }

    public function isVariadic(): bool
    {
        return $this->getReflection()->isVariadic();
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function isPromoted(): bool
    {
        return $this->getReflection()->isPromoted();
    }

    public function getProperty(): ?Property
    {
        if (! isset($this->property)) {
            if ($this->isPromoted()) {
                $this->property = $this->getMethod()->getStructure()->getProperty($this->getName());
            } else {
                $this->property = null;
            }
        }

        return $this->property;
    }

    public function getAllMetadata(): ParameterMetadataCollection
    {
        if (! isset($this->metadata)) {
            $this->metadata = StructureFactory::getInstance()->makeParameterMetadata($this);
        }

        return $this->metadata;
    }
}