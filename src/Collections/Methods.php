<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodCollection;
use Smpl\Inspector\Contracts\MethodFilter;
use Traversable;

class Methods implements MethodCollection
{
    /**
     * @param \Smpl\Inspector\Contracts\Method[] $methods
     *
     * @return array<string, \Smpl\Inspector\Contracts\Method>
     */
    private static function keyByMethodName(array $methods): array
    {
        $keyed = [];

        foreach ($methods as $method) {
            $keyed[$method->getName()] = $method;
        }

        return $keyed;
    }

    /**
     * @var array<string, \Smpl\Inspector\Contracts\Method>
     */
    protected array $methods;

    /**
     * @var array<int, string>
     */
    private array $indexes;

    /**
     * @param \Smpl\Inspector\Contracts\Method[] $methods
     */
    public function __construct(array $methods)
    {
        $this->methods = self::keyByMethodName($methods);
        $this->indexes = array_keys($this->methods);
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
        $name = $this->indexes[$index] ?? null;
        return ($name ? $this->methods[$name] : null) ?? null;
    }

    public function first(): ?MethodContract
    {
        return $this->indexOf(0);
    }

    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    public function filter(MethodFilter $filter): static
    {
        return new self(
            array_filter($this->methods, $filter->check(...))
        );
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function names(): array
    {
        return $this->indexes;
    }
}