<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\MethodFilter;
use Smpl\Inspector\Contracts\Structure;

final class StructureMethods extends Methods
{
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
}