<?php

namespace Smpl\Inspector\Contracts;

use ReflectionProperty;
use Smpl\Inspector\Support\Visibility;

interface Property
{
    public function getReflection(): ReflectionProperty;

    public function getStructure(): Structure;

    public function getName(): string;

    public function getType(): ?Type;

    public function getVisibility(): Visibility;

    public function isStatic(): bool;

    public function isNullable(): bool;

    public function hasDefault(): bool;

    public function getDefault(): mixed;

    public function getAttributes(): PropertyAttributeCollection;
}