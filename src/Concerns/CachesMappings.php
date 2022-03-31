<?php

namespace Smpl\Inspector\Concerns;

/**
 * @psalm-suppress MixedInferredReturnType
 * @psalm-suppress MixedPropertyTypeCoercion
 * @codeCoverageIgnore
 * @infection-ignore-all
 */
trait CachesMappings
{
    private bool $shouldCache = true;

    /**
     * @var array<string, list<class-string>>
     */
    private array $pathCache = [];

    /**
     * @var array<string, list<class-string>>
     */
    private array $namespaceCache = [];

    /**
     * @param string   $path
     * @param callable $store
     *
     * @return list<class-string>
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    protected function getOrStorePathMapping(string $path, callable $store): array
    {
        if (! $this->shouldCache) {
            return $store();
        }

        if (! isset($this->pathCache[$path])) {
            $this->pathCache[$path] = $store();
        }

        return $this->pathCache[$path];
    }

    /**
     * @param string   $namespace
     * @param callable $store
     *
     * @return list<class-string>
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    protected function getOrStoreNamespaceMapping(string $namespace, callable $store): array
    {
        if (! $this->shouldCache) {
            return $store();
        }

        if (! isset($this->namespaceCache[$namespace])) {
            $this->namespaceCache[$namespace] = $store();
        }

        return $this->namespaceCache[$namespace];
    }

    public function dontCache(): static
    {
        $this->shouldCache = false;
        return $this;
    }

    public function cache(): static
    {
        $this->shouldCache = true;
        return $this;
    }

    public function isCaching(): bool
    {
        return $this->shouldCache;
    }
}