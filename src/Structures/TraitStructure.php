<?php

declare(strict_types=1);

namespace Smpl\Inspector\Structures;

use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Support\StructureType;

class TraitStructure extends BaseStructure
{
    public function getStructureType(): StructureType
    {
        return StructureType::Trait;
    }

    public function getParent(): ?StructureContract
    {
        return null;
    }

    public function getInterfaces(): array
    {
        return [];
    }
}