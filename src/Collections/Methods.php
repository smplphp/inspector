<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodCollection;
use Smpl\Inspector\Contracts\MethodFilter;
use Smpl\Inspector\Contracts\Structure;
use Traversable;

class Methods implements MethodCollection
{
    /**
     * @var array<string, \Smpl\Inspector\Contracts\Method>
     */
    protected array $methods;

    /**
     * @var array<int, string>
     */
    private array $indexes;

    /**
     * @param list<\Smpl\Inspector\Contracts\Method> $methods
     */
    public function __construct(array $methods)
    {
        $this->buildMethodsAndIndexes($methods);
    }

    /**
     * Build the methods and index properties.
     *
     * @param list<\Smpl\Inspector\Contracts\Method> $methods
     *
     * @return void
     */
    private function buildMethodsAndIndexes(array $methods): void
    {
        $this->methods = [];
        $this->indexes = [];

        foreach ($methods as $method) {
            $this->methods[$method->getFullName()] = $method;
            $this->indexes[]                       = $method->getFullName();
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->methods);
    }

    public function count(): int
    {
        return count($this->methods);
    }

    public function get(string $name): ?MethodContract
    {
        return $this->methods[$name] ?? null;
    }

    public function indexOf(int $index): ?MethodContract
    {
        if (! isset($this->indexes[$index])) {
            return null;
        }

        return $this->get($this->indexes[$index]);
    }

    public function first(): ?MethodContract
    {
        return $this->indexOf(0);
    }

    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function filter(MethodFilter $filter): static
    {
        $filtered = clone $this;
        $filtered->buildMethodsAndIndexes(array_filter($this->values(), $filter->check(...)));

        return $filtered;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function names(bool $includeClass = true): array
    {
        $names = $this->indexes;

        if (! $includeClass) {
            $names = array_map(
                static function (string $name) {
                    return str_contains($name, Structure::SEPARATOR)
                        ? explode(Structure::SEPARATOR, $name)[1]
                        : $name;
                },
                $names
            );
        }

        return array_unique($names);
    }

    public function values(): array
    {
        return array_values($this->methods);
    }
}