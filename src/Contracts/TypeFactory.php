<?php

namespace Smpl\Inspector\Contracts;

use ReflectionType;

/**
 * Type Factory Contract
 *
 * This contract represents a factory for creating inspector types.
 *
 * @see \Smpl\Inspector\Contracts\Type
 */
interface TypeFactory
{
    /**
     * Make a type for the provided reflection or name.
     *
     * @param \ReflectionType|string|class-string $type
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function make(ReflectionType|string $type): Type;

    /**
     * Make a nullable type from the provided reflection or name.
     *
     * @param \ReflectionType|\Smpl\Inspector\Contracts\Type|string|class-string $type
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function makeNullable(ReflectionType|Type|string $type): Type;

    /**
     * Make a union type for the provided types.
     *
     * @param array<\ReflectionType|\Smpl\Inspector\Contracts\Type|string|class-string> $types
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function makeUnion(array $types): Type;

    /**
     * Make an intersection type for the provided types.
     *
     * @param array<\ReflectionType|\Smpl\Inspector\Contracts\Type|class-string> $types
     *
     * @return \Smpl\Inspector\Contracts\Type
     */
    public function makeIntersection(array $types): Type;
}