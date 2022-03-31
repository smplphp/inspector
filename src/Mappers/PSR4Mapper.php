<?php

declare(strict_types=1);

namespace Smpl\Inspector\Mappers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Smpl\Inspector\Concerns\CachesMappings;
use Smpl\Inspector\Contracts\Mapper;
use Smpl\Inspector\Support\MapperHelper;

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
        $path     = MapperHelper::normalisePath($path);
        $basePath = MapperHelper::normalisePath($basePath);

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
            $fqn = MapperHelper::getPSR4NamespaceFromPath($basePath, $baseNamespace, $file[0]);

            if (MapperHelper::isValidClass($fqn)) {
                $classes[] = ltrim($fqn, MapperHelper::NAMESPACE_SEPARATOR);
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
            $namespace = MapperHelper::normaliseNamespace($namespace);

            foreach ($classPaths as $classPath) {
                if (MapperHelper::isSubDirOf($classPath, $path, $depth)) {
                    $pathCheck = $classPath;
                } else if (MapperHelper::isSubDirOf($path, $classPath, $depth)) {
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
            $psrNamespace = MapperHelper::normaliseNamespace($psrNamespace);

            foreach ($classPaths as $classPath) {
                $realPath = MapperHelper::normalisePath($classPath);

                if (MapperHelper::isSubNamespaceOf($psrNamespace, $namespace, $depth)) {
                    $pathCheck = $realPath;
                } else if (MapperHelper::isSubNamespaceOf($namespace, $psrNamespace, $depth)) {
                    $pathCheck = MapperHelper::getPathFromNamespace($namespace, $psrNamespace, $realPath);
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
        $path = MapperHelper::normalisePath($path);

        return $this->getOrStorePathMapping(
            $path, fn() => $this->mapPathsFromMap($path, $depth)
        );
    }

    public function mapNamespace(string $namespace, ?int $depth = null): array
    {
        $namespace = MapperHelper::normaliseNamespace($namespace);

        return $this->getOrStoreNamespaceMapping(
            $namespace, fn() => $this->mapNamespacesFromMap($namespace, $depth)
        );
    }
}