<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionProperty;
use Smpl\Inspector\Contracts\Metadata;
use Smpl\Inspector\Contracts\Property as PropertyContract;
use Smpl\Inspector\Contracts\PropertyMetadataCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\Visibility;

class InheritedProperty implements PropertyContract
{
    private PropertyContract $property;
    private Structure        $structure;

    public function __construct(Structure $structure, PropertyContract $property)
    {
        $this->structure = $structure;
        $this->property  = $property;
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function isInherited(): bool
    {
        return true;
    }

    public function getFullName(): string
    {
        return $this->getStructure()->getFullName() . Structure::SEPARATOR . $this->getName();
    }

    // @codeCoverageIgnoreStart
    public function hasAttribute(string $attributeClass, bool $instanceOf = false): bool
    {
        return $this->property->hasAttribute($attributeClass, $instanceOf);
    }

    public function getFirstMetadata(string $attributeClass, bool $instanceOf = false): ?Metadata
    {
        return $this->property->getFirstMetadata($attributeClass, $instanceOf);
    }

    public function getMetadata(string $attributeClass, bool $instanceOf = false): array
    {
        return $this->property->getMetadata($attributeClass, $instanceOf);
    }

    public function getAttributes(): array
    {
        return $this->property->getAttributes();
    }

    public function getReflection(): ReflectionProperty
    {
        return $this->property->getReflection();
    }

    public function getDeclaringStructure(): Structure
    {
        return $this->property->getDeclaringStructure();
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    public function getType(): ?Type
    {
        return $this->property->getType();
    }

    public function getVisibility(): Visibility
    {
        return $this->property->getVisibility();
    }

    public function isStatic(): bool
    {
        return $this->property->isStatic();
    }

    public function isNullable(): bool
    {
        return $this->property->isNullable();
    }

    public function hasDefault(): bool
    {
        return $this->property->hasDefault();
    }

    public function getDefault(): mixed
    {
        return $this->property->getDefault();
    }

    public function isPromoted(): bool
    {
        return $this->property->isPromoted();
    }

    public function getAllMetadata(): PropertyMetadataCollection
    {
        return $this->property->getAllMetadata();
    }
    // @codeCoverageIgnoreEnd
}