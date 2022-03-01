<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionClass;
use RuntimeException;
use Smpl\Inspector\Collections\StructureProperties;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\StructureType;

class Structure implements StructureContract
{
    private ReflectionClass             $reflection;
    private StructureType               $structureType;
    private Type                        $type;
    private ?StructureContract          $parent = null;
    private bool                        $hasParent;
    private StructurePropertyCollection $properties;

    public function __construct(ReflectionClass $reflection, StructureType $structureType, Type $type)
    {
        $this->reflection    = $reflection;
        $this->structureType = $structureType;
        $this->type          = $type;
    }

    /**
     * @return array<string, \Smpl\Inspector\Elements\Property>
     */
    private function buildProperties(): array
    {
        $propertyReflections = $this->reflection->getProperties();
        $properties          = [];

        foreach ($propertyReflections as $propertyReflection) {
            $properties[$propertyReflection->getName()] = new Property(
                $this,
                $propertyReflection,
                $propertyReflection->hasType()
                    ? Inspector::getInstance()->types()->make($propertyReflection->getType())
                    : null
            );
        }

        return $properties;
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
                                            ->make($parentReflection);
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
            $this->properties = new StructureProperties(
                $this,
                $this->buildProperties()
            );
        }

        return $this->properties;
    }
}