<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionMethod;
use Smpl\Inspector\Contracts\Metadata;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodMetadataCollection;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\ParameterFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\Visibility;

class InheritedMethod implements MethodContract
{
    private MethodContract $method;
    private Structure      $structure;

    public function __construct(Structure $structure, MethodContract $method)
    {
        $this->structure = $structure;
        $this->method    = $method;
    }

    public function getFullName(): string
    {
        return $this->getStructure()->getFullName() . Structure::SEPARATOR . $this->getName();
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function isInherited(): bool
    {
        return true;
    }

    // @codeCoverageIgnoreStart
    public function hasAttribute(string $attributeClass, bool $instanceOf = false): bool
    {
        return $this->method->hasAttribute($attributeClass, $instanceOf);
    }

    public function getFirstMetadata(string $attributeClass, bool $instanceOf = false): ?Metadata
    {
        return $this->method->getFirstMetadata($attributeClass, $instanceOf);
    }

    public function getMetadata(string $attributeClass, bool $instanceOf = false): array
    {
        return $this->method->getMetadata($attributeClass, $instanceOf);
    }

    public function getAttributes(): array
    {
        return $this->method->getAttributes();
    }

    public function getReflection(): ReflectionMethod
    {
        return $this->method->getReflection();
    }

    public function getName(): string
    {
        return $this->method->getName();
    }

    public function getVisibility(): Visibility
    {
        return $this->method->getVisibility();
    }

    public function isStatic(): bool
    {
        return $this->method->isStatic();
    }

    public function isAbstract(): bool
    {
        return $this->method->isAbstract();
    }

    public function isConstructor(): bool
    {
        return $this->method->isConstructor();
    }

    public function getReturnType(): ?Type
    {
        return $this->method->getReturnType();
    }

    public function getDeclaringStructure(): Structure
    {
        return $this->method->getDeclaringStructure();
    }

    public function getParameters(?ParameterFilter $filter = null): MethodParameterCollection
    {
        return $this->method->getParameters($filter);
    }

    public function getParameter(int|string $parameter): ?Parameter
    {
        return $this->method->getParameter($parameter);
    }

    public function hasParameter(int|string $parameter): bool
    {
        return $this->method->hasParameter($parameter);
    }

    public function getAllMetadata(): MethodMetadataCollection
    {
        return $this->method->getAllMetadata();
    }
    // @codeCoverageIgnoreEnd
}