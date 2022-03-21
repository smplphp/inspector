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
     * @param array<string, \Smpl\Inspector\Contracts\Method> $methods
     *
     * @return array<int, string>
     */
    private static function mapIndexes(array $methods): array
    {
        $indexes = [];

        foreach ($methods as $method) {
            $indexes[] = $method->getName();
        }

        return $indexes;
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
     * @param array<string, \Smpl\Inspector\Contracts\Method> $methods
     */
    public function __construct(array $methods)
    {
        $this->methods   = $methods;
        $this->indexes   = self::mapIndexes($this->methods);
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
}