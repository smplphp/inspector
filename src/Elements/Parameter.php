<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionParameter;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Parameter as ParameterContract;
use Smpl\Inspector\Contracts\ParameterAttributeCollection;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;

class Parameter implements ParameterContract
{
    private Method                       $method;
    private ?Property                    $property;
    private ReflectionParameter          $reflection;
    private ?Type                        $type;
    private ParameterAttributeCollection $attributes;

    public function __construct(Method $method, ReflectionParameter $reflection, ?Type $type = null)
    {
        $this->method     = $method;
        $this->reflection = $reflection;
        $this->type       = $type;
    }

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
        return $this->getReflection()->getDefaultValue();
    }

    public function isVariadic(): bool
    {
        return $this->getReflection()->isVariadic();
    }

    /**
     * @return bool
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function isPromoted(): bool
    {
        return $this->getReflection()->isPromoted();
    }

    public function getProperty(): ?Property
    {
        if (! isset($this->property) && $this->isPromoted()) {
            $this->property = Inspector::getInstance()->structures()->makeProperty(
                $this->getName(),
                $this->getMethod()->getStructure()
            );
        }

        return $this->property;
    }

    public function getAttributes(): ParameterAttributeCollection
    {
        if (! isset($this->attributes)) {
            $this->attributes = Inspector::getInstance()->structures()->makeParameterAttributes($this);
        }

        return $this->attributes;
    }
}