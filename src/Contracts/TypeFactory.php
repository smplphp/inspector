<?php

namespace Smpl\Inspector\Contracts;

use ReflectionType;

interface TypeFactory
{
    public function make(ReflectionType|string $type): Type;

    public function makeNullable(ReflectionType|Type|string $type): Type;

    /**
     * @param array<\ReflectionType|\Smpl\Inspector\Contracts\Type|string> $types
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function makeUnion(array $types): Type;

    /**
     * @param array<\ReflectionType|\Smpl\Inspector\Contracts\Type|string> $types
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function makeIntersection(array $types): Type;
}