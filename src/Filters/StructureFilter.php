<?php

declare(strict_types=1);

namespace Smpl\Inspector\Filters;

use Smpl\Inspector\Contracts\MethodFilter;
use Smpl\Inspector\Contracts\PropertyFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureFilter as StructureFilterContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\StructureType;

final class StructureFilter implements StructureFilterContract
{
    public static function make(): StructureFilter
    {
        return new self;
    }

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

    public function doesNotUse(string $trait): static
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
        return $this->checkTypes($structure)
            && $this->checkNamespace($structure)
            && $this->checkConstructor($structure)
            && $this->checkInstantiable($structure)
            && $this->checkChildOf($structure)
            && $this->checkAcceptedBy($structure)
            && $this->checkImplements($structure)
            && $this->checkUses($structure)
            && $this->checkMethods($structure)
            && $this->checkProperties($structure)
            && $this->checkAttribute($structure);
    }

    private function checkTypes(Structure $structure): bool
    {
        if (empty($this->types)) {
            return true;
        }

        return in_array($structure->getStructureType(), $this->types, true);
    }

    private function checkNamespace(Structure $structure): bool
    {
        if (! isset($this->namespace)) {
            return true;
        }

        return $this->inNamespace === str_starts_with($structure->getNamespace(), $this->namespace);
    }

    private function checkConstructor(Structure $structure): bool
    {
        if (! isset($this->hasConstructor)) {
            return true;
        }

        return $this->hasConstructor === ($structure->getConstructor() !== null);
    }

    private function checkInstantiable(Structure $structure): bool
    {
        if (! isset($this->isInstantiable)) {
            return true;
        }

        return $this->isInstantiable === $structure->isInstantiable();
    }

    private function checkChildOf(Structure $structure): bool
    {
        if (! isset($this->parent)) {
            return true;
        }

        $parent = $structure->getParent();

        if ($parent === null) {
            return $this->isChildOf === false;
        }

        return ($parent->getFullName() === $this->parent) === $this->isChildOf;
    }

    private function checkAcceptedBy(Structure $structure): bool
    {
        if (! isset($this->type)) {
            return true;
        }

        return $this->acceptedBy === $this->type->accepts($structure->getType());
    }

    private function checkImplements(Structure $structure): bool
    {
        if (! isset($this->interface)) {
            return true;
        }

        return $this->implements === $structure->implements($this->interface);
    }

    private function checkUses(Structure $structure): bool
    {
        if (! isset($this->trait)) {
            return true;
        }

        return $this->uses === $structure->uses($this->trait);
    }

    private function checkMethods(Structure $structure): bool
    {
        if (! isset($this->methodFilter)) {
            return true;
        }

        return $structure->getMethods()->filter($this->methodFilter)->isNotEmpty();
    }

    private function checkProperties(Structure $structure): bool
    {
        if (! isset($this->propertyFilter)) {
            return true;
        }

        if (! $structure->getStructureType()->canHaveProperties()) {
            return false;
        }

        return $structure->getProperties()->filter($this->propertyFilter)->isNotEmpty();
    }

    private function checkAttribute(Structure $structure): bool
    {
        if (! isset($this->attribute)) {
            return true;
        }

        return $structure->hasAttribute($this->attribute, $this->attributeInstanceCheck);
    }
}