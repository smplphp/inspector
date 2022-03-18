<?php

namespace Smpl\Inspector\Contracts;

use ReflectionClass;
use Smpl\Inspector\Support\StructureType;

interface Structure
{
    public function getReflection(): ReflectionClass;

    public function getType(): Type;

    public function getStructureType(): StructureType;

    public function getName(): string;

    public function getFullName(): string;

    public function getNamespace(): string;

    public function isInstantiable(): bool;

    public function getConstructor(): ?Method;

    public function getParent(): ?Structure;

    public function getProperties(): StructurePropertyCollection;

    public function getMethods(): StructureMethodCollection;

    public function getAttributes(): StructureAttributeCollection;
}