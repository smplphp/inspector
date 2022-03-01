<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\StructureType;

interface Structure
{
    public function getType(): Type;

    public function getStructureType(): StructureType;

    public function getName(): string;

    public function getFullName(): string;

    public function getNamespace(): string;

    public function isInstantiable(): bool;

    public function getParent(): ?Structure;

    public function getProperties(): StructurePropertyCollection;
}