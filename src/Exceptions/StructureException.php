<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

class StructureException extends InspectorException
{
    public static function methodParametersWithoutClass(string $method): self
    {
        return new self(sprintf(
            'No class or structure was provided when attempting to retrieve parameters for \'%s\'',
            $method
        ));
    }

    public static function invalidClass(string $class): self
    {
        return new self(sprintf(
            'Provided class \'%s\' is not valid',
            $class
        ));
    }

    public static function noProperties(string $class, string $value): self
    {
        return new self(sprintf(
            'Class \'%s\' of type \'%s\' does not support properties',
            $class,
            $value
        ));
    }
}