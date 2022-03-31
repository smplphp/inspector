<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

final class MapperException extends InspectorException
{
    /**
     * @codeCoverageIgnore
     */
    public static function composerClassLoaderMissing(): self
    {
        return new self(
            'The Composer ClassLoader is not registered as an autoloader, and hasn\'t been provided'
        );
    }

    public static function invalidPath(string $path): self
    {
        return new self(sprintf(
            'The provided path \'%s\' is not a valid path for mapping',
            $path
        ));
    }

    public static function invalidFilePath(string $path): self
    {
        return new self(sprintf(
            'The provided path \'%s\' is not a valid file',
            $path
        ));
    }

    public static function invalidClass(string $class): self
    {
        return new self(sprintf(
            'The provided class \'%s\' does not exist, and cannot be autoloaded',
            $class
        ));
    }
}