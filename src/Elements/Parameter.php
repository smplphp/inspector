<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionParameter;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Parameter as ParameterContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;

class Parameter implements ParameterContract
{
    /**
     * @var \Smpl\Inspector\Contracts\Method
     */
    private Method              $method;
    private ReflectionParameter $reflection;
    private Type                $type;

    public function __construct(ReflectionParameter $reflection, Method $method)
    {
        $this->reflection = $reflection;
        $this->method     = $method;
    }

    public function getType(): ?Type
    {
        if (! isset($this->type) && $this->reflection->hasType()) {
            $this->type = Inspector::getInstance()->makeType($this->reflection->getType());
        }

        return $this->type;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getPosition(): int
    {
        return $this->reflection->getPosition();
    }

    public function isNullable(): bool
    {
        return $this->reflection->allowsNull();
    }

    public function hasDefaultValue(): bool
    {
        return ! $this->reflection->isOptional();
    }

    public function getDefaultValue(): mixed
    {
        return $this->reflection->getDefaultValue();
    }

    public function getMethod(): ?Method
    {
        return $this->method;
    }

    public function __toString()
    {
        return $this->getName();
    }
}