<?php

namespace Smpl\Inspector\Contracts;

use Stringable;

/**
 * Type Contract
 *
 * This contract represents a type as part of PHPs typing system.
 */
interface Type extends Stringable
{
    /**
     * The character used to denote a type as nullable.
     */
    public final const NULLABLE_CHARACTER = '?';

    /**
     * The separator used for union types.
     */
    public final const UNION_SEPARATOR = '|';

    /**
     * The separator used for intersection types.
     */
    public final const INTERSECTION_SEPARATOR = '&';

    /**
     * Get the name of the type.
     *
     * @return string|class-string
     */
    public function getName(): string;

    /**
     * Check if the provided value is valid for this type.
     *
     * This method deals specifically with values, to see if they are of the
     * correct type. If you want to compare types, use
     * {@see \Smpl\Inspector\Contracts\Type::accepts()}.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches(mixed $value): bool;

    /**
     * Check if the provided type would be accepted as valid for this type.
     *
     * This method deals with types, to see if different types are acceptable.
     * If you want to see if a value is valid, use
     * {@see \Smpl\Inspector\Contracts\Type::matches()}.
     *
     * @param \Smpl\Inspector\Contracts\Type|class-string|string $type
     *
     * @return bool
     */
    public function accepts(Type|string $type): bool;

    /**
     * Check if the type is primitive.
     *
     * Primitive types are essentially everything but a class and 'object'.
     *
     * @return bool
     */
    public function isPrimitive(): bool;

    /**
     * Check if the type is built in.
     *
     * Builtin types are typically all non-class based types, such as string,
     * int, float, etc. Classes like {@see \stdClass}, {@see \Closure} etc, are
     * considered internal, using {@see \ReflectionClass::isInternal()}, not
     * builtin.
     *
     * @return bool
     */
    public function isBuiltin(): bool;

    /**
     * Check if the type is nullable.
     *
     * @return bool
     */
    public function isNullable(): bool;
}