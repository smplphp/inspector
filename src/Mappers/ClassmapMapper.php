<?php

declare(strict_types=1);

namespace Smpl\Inspector\Mappers;

use Smpl\Inspector\Concerns\CachesMappings;
use Smpl\Inspector\Contracts\Mapper;
use Smpl\Inspector\Exceptions\MapperException;
use Smpl\Inspector\Support\MapperHelper;

class ClassmapMapper implements Mapper
{
    use CachesMappings;

    /**
     * @var array<class-string, string>
     */
    private array $classmap;

    /**
     * @param array<class-string, string> $classmap
     */
    public function __construct(array $classmap)
    {
        $this->classmap = $classmap;
    }

    /**
     * @param string   $path
     * @param int|null $depth
     *
     * @return list<class-string>
     *
     * @throws \Smpl\Inspector\Exceptions\MapperException
     */
    private function mapPathsFromClassMap(string $path, ?int $depth = null): array
    {
        $classes = [];

        foreach ($this->classmap as $class => $classPath) {
            if (MapperHelper::isFileInDir($classPath, $path, $depth)) {
                if (! MapperHelper::isValidClass($class)) {
                    throw MapperException::invalidClass($class);
                }

                $classes[] = $class;
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
     *
     * @psalm-suppress UnusedForeachValue
     */
    private function mapNamespacesFromClassMap(string $namespace, ?int $depth = null): array
    {
        $classes = [];

        foreach ($this->classmap as $class => $classPath) {
            if (MapperHelper::isClassInNamespace($class, $namespace, $depth)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    public function mapPath(string $path, ?int $depth = null): array
    {
        $path = MapperHelper::normalisePath($path);

        return $this->getOrStorePathMapping(
            $path, fn() => $this->mapPathsFromClassMap($path, $depth)
        );
    }

    public function mapNamespace(string $namespace, ?int $depth = null): array
    {
        return $this->getOrStoreNamespaceMapping(
            $namespace, fn() => $this->mapNamespacesFromClassMap($namespace, $depth)
        );
    }
}