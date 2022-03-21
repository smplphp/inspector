<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureMethodCollection;
use Traversable;

final class StructureMethods implements StructureMethodCollection
{
    private static function mapIndexes(array $methods): array
    {
        $indexes = [];

        foreach ($methods as $name => $method) {
            $indexes[] = $name;
        }

        return $indexes;
    }

    private Structure $structure;

    /**
     * @var array<string, \Smpl\Inspector\Contracts\Method>
     */
    private array $methods;

    /**
     * @var array<int, string>
     */
    private array $indexes;

    /**
     * @param \Smpl\Inspector\Contracts\Structure             $structure
     * @param array<string, \Smpl\Inspector\Contracts\Method> $methods
     */
    public function __construct(Structure $structure, array $methods)
    {
        $this->structure = $structure;
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

    public function getStructure(): Structure
    {
        return $this->structure;
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

    public function filter(MethodFilter $filter): StructureMethodCollection
    {
        return new self(
            $this->getStructure(),
            array_filter($this->methods, $filter->check(...))
        );
    }
}