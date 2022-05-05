<?php

declare(strict_types=1);

namespace Smpl\Inspector\Mappers;

use Smpl\Inspector\Concerns\CachesMappings;
use Smpl\Inspector\Contracts\Mapper;
use Smpl\Inspector\Support\ClassHelper;
use Smpl\Inspector\Support\PathHelper;

class MultiMapper implements Mapper
{
    use CachesMappings;

    /**
     * @var list<\Smpl\Inspector\Contracts\Mapper>
     */
    private array $mappers;

    /**
     * @param list<\Smpl\Inspector\Contracts\Mapper> $mappers
     */
    public function __construct(array $mappers)
    {
        $this->mappers = $mappers;

        foreach ($this->mappers as $mapper) {
            if (method_exists($mapper, 'dontCache')) {
                $mapper->dontCache();
            }
        }
    }

    public function mapPath(string $path, ?int $depth = null): array
    {
        $path = PathHelper::normalisePath($path);

        return $this->getOrStorePathMapping($path, function () use ($depth, $path) {
            $classes = [];

            foreach ($this->mappers as $mapper) {
                $mapperClasses = $mapper->mapPath($path, $depth);

                if (! empty($mapperClasses)) {
                    $classes[] = $mapperClasses;
                }
            }

            return array_merge(...$classes);
        });
    }

    /**
     * @return \Smpl\Inspector\Contracts\Mapper[]
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }

    public function mapNamespace(string $namespace, ?int $depth = null): array
    {
        $namespace = ClassHelper::normaliseNamespace($namespace);

        return $this->getOrStoreNamespaceMapping($namespace, function () use ($depth, $namespace) {
            $classes = [];

            foreach ($this->mappers as $mapper) {
                $mapperClasses = $mapper->mapNamespace($namespace, $depth);

                if (! empty($mapperClasses)) {
                    $classes[] = $mapperClasses;
                }
            }

            return array_merge(...$classes);
        });
    }
}