<?php

declare(strict_types=1);

namespace Smpl\Inspector\Structures;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Elements\Method as MethodElement;
use Smpl\Inspector\Elements\Property;
use Smpl\Inspector\Inspector;

abstract class BaseStructure implements Structure
{
    private ReflectionClass $reflection;
    private Type            $type;
    private array           $methods     = [];
    private array           $properties  = [];
    private ?Method         $constructor = null;
    private bool            $hasParent;
    private ?Structure      $parent;
    private array           $interfaces  = [];

    public function __construct(ReflectionClass $reflection, Type $type)
    {
        $this->reflection = $reflection;
        $this->type       = $type;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getType(): Type
    {
        return $this->type;
    }

    private function createMethods(): void
    {
        $reflectionMethods = $this->reflection->getMethods();

        if (count($reflectionMethods) === 0) {
            return;
        }

        $this->methods = array_map(
            fn(ReflectionMethod $method) => new MethodElement($method, $this),
            $reflectionMethods
        );

        foreach ($this->methods as $method) {
            if ($method->isConstructor()) {
                $this->constructor = $method;
                break;
            }
        }
    }

    public function getMethods(): array
    {
        if (empty($this->methods)) {
            $this->createMethods();
        }

        return $this->methods;
    }

    private function createProperties(): void
    {
        $reflectionProperties = $this->reflection->getProperties();

        if (count($reflectionProperties) === 0) {
            return;
        }

        $this->properties = array_map(
            fn(ReflectionProperty $property) => new Property($property, $this),
            $reflectionProperties
        );
    }

    public function getProperties(): array
    {
        if (empty($this->properties)) {
            $this->createProperties();
        }

        return $this->properties;
    }

    public function getConstructor(): ?Method
    {
        return $this->constructor;
    }

    public function isInternal(): bool
    {
        return $this->reflection->isInternal();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function isInstantiable(): bool
    {
        return $this->reflection->isInstantiable();
    }

    private function createParent()
    {
        $reflectionParent = null;

        if (! isset($this->hasParent)) {
            $reflectionParent = $this->reflection->getParentClass();
            $this->hasParent  = $reflectionParent !== null;
        }

        if ($this->hasParent) {
            $this->parent = Inspector::getInstance()->makeStructure($reflectionParent);
        }
    }

    public function getParent(): ?Structure
    {
        if (! isset($this->parent)) {
            $this->createParent();
        }

        return $this->parent;
    }

    private function createInterfaces(): void
    {
        $this->interfaces = array_map(
            fn(ReflectionClass $reflection) => Inspector::getInstance()->makeStructure(...),
            $this->reflection->getInterfaces()
        );
    }

    public function getInterfaces(): array
    {
        if (empty($this->interfaces)) {
            $this->createInterfaces();
        }

        return $this->interfaces;
    }
}