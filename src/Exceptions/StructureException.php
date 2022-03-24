<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

use Throwable;

final class StructureException extends InspectorException
{
    public static function functions(): self
    {
        return new self('Functions are not currently supported');
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