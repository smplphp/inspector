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

    public static function invalidNullable(): self
    {
        return new self('Nullable types cannot include union or intersection types');
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

    public static function invalidBase(string $type): self
    {
        return new self(sprintf(
            'Cannot create a type for the provided base type \'%s\'',
            $type
        ));
    }

    public static function invalid(string $type): self
    {
        return new self(sprintf(
            'Cannot create a type for the provided value \'%s\'',
            $type
        ));
    }
}