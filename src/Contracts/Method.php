<?php

namespace Smpl\Inspector\Contracts;

use ReflectionMethod;
use Smpl\Inspector\Support\Visibility;

interface Method
{
    public function getReflection(): ReflectionMethod;

    public function getName(): string;

    public function getFullName(): string;

    public function getVisibility(): Visibility;

    public function isStatic(): bool;

    public function isAbstract(): bool;

    public function getReturnType(): ?Type;

    public function getStructure(): Structure;

    public function getParameters(): MethodParameterCollection;
}