<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

use Smpl\Inspector\Exceptions\MapperException;

final class ClassHelper
{
    public final const NAMESPACE_SEPARATOR = '\\';

    /**
     * @param string $basePath
     * @param string $baseNamespace
     * @param string $path
     *
     * @return string|class-string
     */
    public static function getPSR4NamespaceFromPath(string $basePath, string $baseNamespace, string $path): string
    {
        return $baseNamespace
            . str_replace(
                [$basePath, DIRECTORY_SEPARATOR, '.php'],
                ['', ClassHelper::NAMESPACE_SEPARATOR, ''],
                $path
            );
    }

    public static function getPathFromNamespace(string $namespace, string $baseNamespace, string $basePath): string
    {
        return $basePath
            . str_replace(
                [$baseNamespace, ClassHelper::NAMESPACE_SEPARATOR],
                ['', DIRECTORY_SEPARATOR],
                $namespace
            );
    }

    public static function isClassInNamespace(string $class, string $namespace, ?int $depth = null): bool
    {
        $realClass     = self::normaliseNamespace($class);
        $realNamespace = self::normaliseNamespace($namespace);

        if (! self::isValidClass($realClass)) {
            throw MapperException::invalidClass($class);
        }

        $intersects = str_starts_with($realClass, $realNamespace);

        if ($depth === null || ! $intersects) {
            return $intersects;
        }

        $classNamespace = implode(self::NAMESPACE_SEPARATOR, explode(self::NAMESPACE_SEPARATOR, $realClass, -1));
        $difference     = str_replace($realNamespace, self::NAMESPACE_SEPARATOR, $classNamespace);

        return substr_count($difference, self::NAMESPACE_SEPARATOR) <= $depth;
    }

    public static function isSubNamespaceOf(string $subNamespace, string $namespace, ?int $depth = null): bool
    {
        $realSubNamespace = self::normaliseNamespace($subNamespace);
        $realNamespace    = self::normaliseNamespace($namespace);

        $intersects = str_starts_with($realSubNamespace, $realNamespace);

        if ($depth === null || ! $intersects) {
            return $intersects;
        }

        $subSubNamespace = implode(self::NAMESPACE_SEPARATOR, explode(self::NAMESPACE_SEPARATOR, $realSubNamespace, -1));
        $difference      = str_replace($realNamespace, self::NAMESPACE_SEPARATOR, $subSubNamespace);

        return substr_count($difference, self::NAMESPACE_SEPARATOR) <= $depth;
    }

    /**
     * @param class-string|string $class
     *
     * @return bool
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public static function isValidClass(string $class): bool
    {
        return class_exists($class)
            || interface_exists($class)
            || trait_exists($class)
            || enum_exists($class);
    }

    public static function normaliseNamespace(string $namespace): string
    {
        if (! str_starts_with($namespace, self::NAMESPACE_SEPARATOR)) {
            $namespace = self::NAMESPACE_SEPARATOR . $namespace;
        }

        if (self::isValidClass($namespace)) {
            return $namespace;
        }

        if (! str_ends_with($namespace, self::NAMESPACE_SEPARATOR)) {
            $namespace .= self::NAMESPACE_SEPARATOR;
        }

        return $namespace;
    }
}