<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

use Throwable;

final class StructureException extends InspectorException
{
    public static function functions(): self
    {
        return new self('Functions are currently not supported');
    }

    public static function methodParametersWithoutClass(string $method): self
    {
        return new self(sprintf(
            'No class or structure was provided when attempting to retrieve parameters for \'%s\'',
            $method
        ));
    }

    /**
     * An exception for when the provided class name is invalid.
     *
     * Invalid classes typically don't exist, or are unable to be reflected
     * for some other reason.
     *
     * @param string          $class
     * @param \Throwable|null $previous
     *
     * @return static
     */
    public static function invalidClass(string $class, ?Throwable $previous = null): self
    {
        return new self(sprintf(
            'Provided class \'%s\' is not valid',
            $class,
        ), previous: $previous);
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