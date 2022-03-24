<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionClass;
use RuntimeException;
use Smpl\Inspector\Collections\Structures;
use Smpl\Inspector\Concerns\HasAttributes;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Contracts\StructureCollection;
use Smpl\Inspector\Contracts\StructureMetadataCollection;
use Smpl\Inspector\Contracts\StructureMethodCollection;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\StructureType;

class Structure implements StructureContract
{
    use HasAttributes;

    private ReflectionClass             $reflection;
    private StructureType               $structureType;
    private Type                        $type;
    private ?StructureContract          $parent = null;
    private bool                        $hasParent;
    private StructurePropertyCollection $properties;
    private StructureMethodCollection   $methods;
    private StructureMetadataCollection $metadata;
    private StructureCollection         $interfaces;
    private StructureCollection         $traits;

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
            $this->properties = Inspector::getInstance()->structures()->makeStructureProperties($this);
        }

        return $this->properties;
    }

    public function getProperty(string $name): ?Property
    {
        return $this->getProperties()->get($name);
    }

    public function hasProperty(string $name): bool
    {
        return $this->getProperties()->has($name);
    }

    public function getMethods(): StructureMethodCollection
    {
        if (! isset($this->methods)) {
            $this->methods = Inspector::getInstance()->structures()->makeStructureMethods($this);
        }

        return $this->methods;
    }

    /**
     * @return \Smpl\Inspector\Contracts\Method|null
     * @throws \Exception
     */
    public function getConstructor(): ?Method
    {
        return $this->getMethod('__construct');
    }

    public function getMethod(string $name): ?Method
    {
        return $this->getMethods()->get($name);
    }

    public function hasMethod(string $name): bool
    {
        return $this->getMethods()->has($name);
    }

    public function getAllMetadata(): StructureMetadataCollection
    {
        if (! isset($this->metadata)) {
            $this->metadata = Inspector::getInstance()->structures()->makeStructureMetadata($this);
        }

        return $this->metadata;
    }

    public function getInterfaces(): StructureCollection
    {
        if (! isset($this->interfaces)) {
            $interfaces = [];
            $inspector  = Inspector::getInstance();

            foreach ($this->getReflection()->getInterfaces() as $reflection) {
                $interfaces[] = $inspector->structures()->makeStructure($reflection->getName());
            }

            $this->interfaces = new Structures($interfaces);
        }

        return $this->interfaces;
    }

    public function getTraits(): StructureCollection
    {
        if (! isset($this->traits)) {
            $traits    = [];
            $inspector = Inspector::getInstance();

            foreach ($this->getReflection()->getTraits() as $reflection) {
                $traits[] = $inspector->structures()->makeStructure($reflection->getName());
            }

            $this->traits = new Structures($traits);
        }

        return $this->traits;
    }

    public function implements(string|StructureContract $interface): bool
    {
        return $this->getInterfaces()->has(
            $interface instanceof StructureContract ? $interface->getFullName() : $interface
        );
    }

    public function uses(string|StructureContract $trait): bool
    {
        return $this->getTraits()->has(
            $trait instanceof StructureContract ? $trait->getFullName() : $trait
        );
    }
}