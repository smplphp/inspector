<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

use Smpl\Inspector\Exceptions\MapperException;

final class PathHelper
{
    public static function normalisePath(string $path): string
    {
        $realPath = realpath($path);

        if ($realPath === false) {
            throw MapperException::invalidPath($path);
        }

        if (is_file($realPath)) {
            return $realPath;
        }

        if (is_dir($realPath)) {
            if (! str_ends_with($realPath, DIRECTORY_SEPARATOR)) {
                $realPath .= DIRECTORY_SEPARATOR;
            }

            return $realPath;
        }

        // This should literally never happen
        // @codeCoverageIgnoreStart
        throw MapperException::invalidPath($path);
        // @codeCoverageIgnoreEnd
    }

    public static function isFileInDir(string $file, string $directory, ?int $depth = null): bool
    {
        $filePath      = self::normalisePath($file);
        $directoryPath = self::normalisePath($directory);

        if (! is_file($filePath)) {
            throw MapperException::invalidFilePath($file);
        }

        $intersects = str_starts_with($filePath, $directoryPath);

        if ($depth === null || ! $intersects) {
            return $intersects;
        }

        $fileDirectory = dirname($filePath);
        $difference    = str_replace($directoryPath, DIRECTORY_SEPARATOR, $fileDirectory);

        return substr_count($difference, DIRECTORY_SEPARATOR) <= $depth;
    }

    public static function isSubDirOf(string $subdirectory, string $directory, ?int $depth = null): bool
    {
        $subdirectoryPath = self::normalisePath($subdirectory);
        $directoryPath    = self::normalisePath($directory);

        $intersects = str_starts_with($subdirectoryPath, $directoryPath);

        if ($depth === null || ! $intersects) {
            return $intersects;
        }

        $subdirectoryDirectory = dirname($subdirectoryPath);
        $difference            = str_replace($directoryPath, DIRECTORY_SEPARATOR, $subdirectoryDirectory);

        return substr_count($difference, DIRECTORY_SEPARATOR) <= $depth;
    }

}