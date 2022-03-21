<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\MethodFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureMethodCollection;
use Smpl\Inspector\Filters\MethodFilter as Filter;

final class StructureMethods extends Methods implements StructureMethodCollection
{
    /**
     * @var \Smpl\Inspector\Contracts\Structure
     */
    private Structure $structure;

    /**
     * @param \Smpl\Inspector\Contracts\Structure             $structure
     * @param array<string, \Smpl\Inspector\Contracts\Method> $methods
     */
    public function __construct(Structure $structure, array $methods)
    {
        $this->structure = $structure;
        parent::__construct($methods);
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function filter(MethodFilter $filter): static
    {
        return new self(
            $this->getStructure(),
            array_filter($this->methods, $filter->check(...))
        );
    }

    public function countInherited(): int
    {
        return abs($this->count() - $this->countDeclared());
    }

    public function countDeclared(): int
    {
        return count(
            $this->filter(Filter::make()->declaredBy($this->getStructure()))
        );
    }
}