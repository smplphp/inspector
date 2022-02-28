<?php

namespace Smpl\Inspector\Contracts;

use ReflectionType;

interface TypeFactory
{
    public function make(ReflectionType|string $type): Type;

    public function makeNullable(ReflectionType|Type|string $type): Type;

    public function makeUnion(ReflectionType|Type|string ...$types): Type;

    public function makeIntersection(ReflectionType|Type|string ...$types): Type;
}