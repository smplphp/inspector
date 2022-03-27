<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureCollection;
use Smpl\Inspector\Contracts\StructureFilter;
use Traversable;

class Structures implements StructureCollection
{
    /**
     * @var array<class-string, \Smpl\Inspector\Contracts\Structure>
     */
    private array $structures;

    /**
     * @var array<int, class-string>
     */
    private array $indexes;

    /**
     * @param list<\Smpl\Inspector\Contracts\Structure> $structures
     */
    public function __construct(array $structures)
    {
        $this->buildStructuresAndIndexes($structures);
    }

    /**
     * Build the structure and index properties.
     *
     * @param list<\Smpl\Inspector\Contracts\Structure> $structures
     *
     * @return void
     */
    private function buildStructuresAndIndexes(array $structures): void
    {
        $this->structures = [];
        $this->indexes    = [];

        foreach ($structures as $structure) {
            $this->structures[$structure->getFullName()] = $structure;
            $this->indexes[]                             = $structure->getFullName();
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->structures);
    }

    public function count(): int
    {
        return count($this->structures);
    }

    public function get(string $name): ?Structure
    {
        return $this->structures[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    public function indexOf(int $index): ?Structure
    {
        if (isset($this->indexes[$index])) {
            return $this->get($this->indexes[$index]);
        }

        return null;
    }

    public function first(): ?Structure
    {
        return $this->indexOf(0);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function filter(StructureFilter $filter): static
    {
        $filtered = clone $this;
        $filtered->buildStructuresAndIndexes(array_filter($this->values(), $filter->check(...)));

        return $filtered;
    }

    /**
     * @infection-ignore-all
     */
    public function names(): array
    {
        return array_values($this->indexes);
    }

    public function values(): array
    {
        return array_values($this->structures);
    }
}