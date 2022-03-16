<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureMethodCollection;
use Traversable;

final class StructureMethods implements StructureMethodCollection
{
    private Structure $structure;

    /**
     * @var array<string, \Smpl\Inspector\Contracts\Method>
     */
    private array $methods;

    /**
     * @param \Smpl\Inspector\Contracts\Structure             $structure
     * @param array<string, \Smpl\Inspector\Contracts\Method> $methods
     */
    public function __construct(Structure $structure, array $methods)
    {
        $this->structure = $structure;
        $this->methods   = $methods;
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