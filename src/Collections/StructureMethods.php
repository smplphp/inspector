<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\MethodCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureMethodCollection;

final class StructureMethods extends Methods implements StructureMethodCollection
{
    public static function for(Structure $structure, MethodCollection $methods): self
    {
        return new self($structure, $methods->values());
    }

    /**
     * @var \Smpl\Inspector\Contracts\Structure
     */
    private Structure $structure;

    /**
     * @param \Smpl\Inspector\Contracts\Structure    $structure
     * @param list<\Smpl\Inspector\Contracts\Method> $methods
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
}