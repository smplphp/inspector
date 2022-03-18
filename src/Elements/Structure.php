<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionClass;
use RuntimeException;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Contracts\StructureAttributeCollection;
use Smpl\Inspector\Contracts\StructureMethodCollection;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\StructureType;

class Structure implements StructureContract
{
    private ReflectionClass              $reflection;
    private StructureType                $structureType;
    private Type                         $type;
    private ?StructureContract           $parent = null;
    private bool                         $hasParent;
    private StructurePropertyCollection  $properties;
    private StructureMethodCollection    $methods;
    private StructureAttributeCollection $attributes;

    public function __construct(ReflectionClass $reflection, StructureType $structureType, Type $type)
    {
        $this->reflection    = $reflection;
        $this->structureType = $structureType;
        $this->type          = $type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getReflection(): ReflectionClass
    {
        return $this->reflection;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getStructureType(): StructureType
    {
        return $this->structureType;
    }

    public function getName(): string
    {
        return $this->reflection->getShortName();
    }

    /**
     * @return class-string
     */
    public function getFullName(): string
    {
        return $this->reflection->getName();
    }

    public function getNamespace(): string
    {
        return $this->reflection->getNamespaceName();
    }

    public function isInstantiable(): bool
    {
        return $this->reflection->isInstantiable();
    }

    public function getParent(): ?StructureContract
    {
        if (! isset($this->hasParent)) {
            $parentReflection = $this->reflection->getParentClass();

            if ($parentReflection !== false) {
                $this->hasParent = true;
                $this->parent    = Inspector::getInstance()
                                            ->structures()
                                            ->makeStructure($parentReflection);
            } else {
                $this->hasParent = false;
                $this->parent    = null;
            }
        }

        return $this->parent;
    }

    public function getProperties(): StructurePropertyCollection
    {
        if (! $this->getStructureType()->canHaveProperties()) {
            throw new RuntimeException(sprintf(
                'Structures of type \'%s\' do not have properties',
                $this->getStructureType()->value
            ));
        }

        if (! isset($this->properties)) {
            $this->properties = Inspector::getInstance()->structures()->makeProperties($this);
        }

        return $this->properties;
    }

    public function getMethods(): StructureMethodCollection
    {
        if (! isset($this->methods)) {
            $this->methods = Inspector::getInstance()->structures()->makeMethods($this);
        }

        return $this->methods;
    }

    /**
     * @return \Smpl\Inspector\Contracts\Method|null
     * @throws \Exception
     */
    public function getConstructor(): ?Method
    {
        foreach ($this->getMethods() as $method) {
            if ($method->isConstructor()) {
                return $method;
            }
        }

        return null;
    }

    public function getAttributes(): StructureAttributeCollection
    {
        if (! isset($this->attributes)) {
            $this->attributes = Inspector::getInstance()->structures()->makeStructureAttributes($this);
        }

        return $this->attributes;
    }
}