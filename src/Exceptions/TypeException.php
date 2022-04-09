<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

final class TypeException extends InspectorException
{
    public static function invalidIntersection(string $type): self
    {
        return new self(sprintf(
            'Invalid intersection type \'%s\'',
            $type
        ));
    }

    public static function invalidNullable(string $type): self
    {
        return new self(sprintf('Nullable types cannot include \'%s\'', $type));
    }

    public static function invalidType(string $type): self
    {
        return new self(sprintf('The provided type \'%s\' is not valid', $type));
    }

    public static function invalidUnion(string $type): self
    {
        return new self(sprintf(
            'Invalid union type \'%s\'',
            $type
        ));
    }

    public static function invalidVoid(): self
    {
        return new self('Void types cannot be nullable, or part of a union type');
    }

    public static function invalidMixed(): self
    {
        return new self('Mixed types cannot be nullable, or part of a union type');
    }

    public static function invalidSelf(): self
    {
        return new self('Self types must represent a valid class');
    }

    public static function invalidStatic(): self
    {
        return new self('Static types must represent a valid class');
    }

    public static function invalid(string $type): self
    {
        return new self(sprintf(
            'Cannot create a type for the provided value \'%s\'',
            $type
        ));
    }
}