<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

class StructureException extends InspectorException
{
    public static function methodParametersWithoutClass(string $method): static
    {
        return new static(sprintf(
            'No class or structure was provided when attempting to retrieve parameters for \'%s\'',
            $method
        ));
    }

    public static function invalidClass(string $class): static
    {
        return new static(sprintf(
            'Provided class \'%s\' is not valid',
            $class
        ));
    }

    public static function noProperties(string $class, string $value): static
    {
        return new static(sprintf(
            'Class \'%s\' of type \'%s\' does not support properties',
            $class,
            $value
        ));
    }
}