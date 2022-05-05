<?php

declare(strict_types=1);

namespace Smpl\Inspector\Mappers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Smpl\Inspector\Concerns\CachesMappings;
use Smpl\Inspector\Contracts\Mapper;
use Smpl\Inspector\Support\ClassHelper;
use Smpl\Inspector\Support\PathHelper;

class PSR4Mapper implements Mapper
{
    use CachesMappings;

    /**
     * @var array<string, list<string>>
     */
    private array $map;

    /**
     * @param array<string, list<string>> $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param string             $path
     * @param string             $baseNamespace
     * @param string             $basePath
     * @param list<class-string> $classes
     *
     * @return void
     *
     * @throws \Smpl\Inspector\Exceptions\MapperException
     *
     * @psalm-suppress ReferenceConstraintViolation
     */
    private function findClassesIn(string $path, string $baseNamespace, string $basePath, array &$classes): void
    {
        $path     = PathHelper::normalisePath($path);
        $basePath = PathHelper::normalisePath($basePath);

        $files = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            ),
            '/^.+\.php$/i',
            RegexIterator::GET_MATCH
        );

        /**
         * @psalm-suppress MixedArgument
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedAssignment
         */
        foreach ($files as $file) {
            /**
             * @var array<string> $file
             */
            $fqn = ClassHelper::getPSR4NamespaceFromPath($basePath, $baseNamespace, $file[0]);

            if (ClassHelper::isValidClass($fqn)) {
                $classes[] = ltrim($fqn, ClassHelper::NAMESPACE_SEPARATOR);
            }
        }
    }

    /**
     * @param string   $path
     * @param int|null $depth
     *
     * @return list<class-string>
     *
     * @throws \Smpl\Inspector\Exceptions\MapperException
     */
    private function mapPathsFromMap(string $path, ?int $depth = null): array
    {
        $classes = [];

        foreach ($this->map as $namespace => $classPaths) {
            $namespace = ClassHelper::normaliseNamespace($namespace);

            foreach ($classPaths as $classPath) {
                if (PathHelper::isSubDirOf($classPath, $path, $depth)) {
                    $pathCheck = $classPath;
                } else if (PathHelper::isSubDirOf($path, $classPath, $depth)) {
                    $pathCheck = $path;
                } else {
                    /** @infection-ignore-all */
                    continue;
                }

                $this->findClassesIn($pathCheck, $namespace, $classPath, $classes);
            }
        }

        return $classes;
    }

    /**
     * @param string   $namespace
     * @param int|null $depth
     *
     * @return list<class-string>
     *
     * @throws \Smpl\Inspector\Exceptions\MapperException
     */
    private function mapNamespacesFromMap(string $namespace, ?int $depth = null): array
    {
        $classes = [];

        foreach ($this->map as $psrNamespace => $classPaths) {
            $psrNamespace = ClassHelper::normaliseNamespace($psrNamespace);

            foreach ($classPaths as $classPath) {
                $realPath = PathHelper::normalisePath($classPath);

                if (ClassHelper::isSubNamespaceOf($psrNamespace, $namespace, $depth)) {
                    $pathCheck = $realPath;
                } else if (ClassHelper::isSubNamespaceOf($namespace, $psrNamespace, $depth)) {
                    $pathCheck = ClassHelper::getPathFromNamespace($namespace, $psrNamespace, $realPath);
                } else {
                    /** @infection-ignore-all */
                    continue;
                }

                $this->findClassesIn($pathCheck, $psrNamespace, $realPath, $classes);
            }
        }

        return $classes;
    }

    public function mapPath(string $path, ?int $depth = null): array
    {
        $path = PathHelper::normalisePath($path);

        return $this->getOrStorePathMapping(
            $path, fn() => $this->mapPathsFromMap($path, $depth)
        );
    }

    public function mapNamespace(string $namespace, ?int $depth = null): array
    {
        $namespace = ClassHelper::normaliseNamespace($namespace);

        return $this->getOrStoreNamespaceMapping(
            $namespace, fn() => $this->mapNamespacesFromMap($namespace, $depth)
        );
    }
}