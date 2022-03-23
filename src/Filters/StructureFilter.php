<?php

declare(strict_types=1);

namespace Smpl\Inspector\Filters;

use Smpl\Inspector\Contracts\MethodFilter;
use Smpl\Inspector\Contracts\PropertyFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureFilter as StructureFilterContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\StructureType;

class StructureFilter implements StructureFilterContract
{
    /**
     * @var \Smpl\Inspector\Support\StructureType[]
     */
    private array  $types = [];
    private string $namespace;
    private bool   $inNamespace;
    private bool   $hasConstructor;
    private bool   $isInstantiable;
    /**
     * @var class-string
     */
    private string $parent;
    private bool   $isChildOf;
    private Type   $type;
    private bool   $acceptedBy;

    /**
     * @var class-string
     */
    private string $interface;
    private bool   $implements;

    /**
     * @var trait-string
     */
    private string         $trait;
    private bool           $uses;
    private MethodFilter   $methodFilter;
    private PropertyFilter $propertyFilter;

    /**
     * @var class-string
     */
    protected string $attribute;
    private bool     $attributeInstanceCheck;

    public function ofType(StructureType $type): static
    {
        $this->types = [$type];
        return $this;
    }

    public function ofTypes(StructureType ...$types): static
    {
        $this->types = $types;
        return $this;
    }

    public function inNamespace(string $namespace): static
    {
        $this->namespace   = $namespace;
        $this->inNamespace = true;
        return $this;
    }

    public function notInNamespace(string $namespace): static
    {
        $this->namespace   = $namespace;
        $this->inNamespace = false;
        return $this;
    }

    public function hasConstructor(): static
    {
        $this->hasConstructor = true;
        return $this;
    }

    public function hasNoConstructor(): static
    {
        $this->hasConstructor = false;
        return $this;
    }

    public function isInstantiable(): static
    {
        $this->isInstantiable = true;
        return $this;
    }

    public function isNotInstantiable(): static
    {
        $this->isInstantiable = false;
        return $this;
    }

    public function childOf(string $class): static
    {
        $this->parent    = $class;
        $this->isChildOf = true;
        return $this;
    }

    public function notChildOf(string $class): static
    {
        $this->parent    = $class;
        $this->isChildOf = false;
        return $this;
    }

    public function acceptedBy(Type $type): static
    {
        $this->type       = $type;
        $this->acceptedBy = true;
        return $this;
    }

    public function notAcceptedBy(Type $type): static
    {
        $this->type       = $type;
        $this->acceptedBy = false;
        return $this;
    }

    public function implements(string $interface): static
    {
        $this->interface  = $interface;
        $this->implements = true;
        return $this;
    }

    public function doesNotImplement(string $interface): static
    {
        $this->interface  = $interface;
        $this->implements = false;
        return $this;
    }

    public function uses(string $trait): static
    {
        $this->trait = $trait;
        $this->uses  = true;
        return $this;
    }

    public function doesNoUse(string $trait): static
    {
        $this->trait = $trait;
        $this->uses  = false;
        return $this;
    }

    public function methodsMatch(MethodFilter $filter): static
    {
        $this->methodFilter = $filter;
        return $this;
    }

    public function propertiesMatch(PropertyFilter $filter): static
    {
        $this->propertyFilter = $filter;
        return $this;
    }

    public function hasAttribute(string $attribute, bool $instanceOf = false): static
    {
        $this->attribute              = $attribute;
        $this->attributeInstanceCheck = $instanceOf;
        return $this;
    }

    public function check(Structure $structure): bool
    {
        return false;
    }
}