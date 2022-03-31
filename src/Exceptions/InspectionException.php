<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

use Throwable;

final class InspectionException extends InspectorException
{
    public static function noSource(): self
    {
        return new self(
            'No paths, classes or namespaces were provided to inspect'
        );
    }

    public static function somethingWrong(Throwable $throwable): self
    {
        return new self(
            'Something went wrong during inspection',
            previous: $throwable
        );
    }
}