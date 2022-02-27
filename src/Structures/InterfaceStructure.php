<?php

declare(strict_types=1);

namespace Smpl\Inspector\Structures;

use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Support\StructureType as StructureEnum;

class InterfaceStructure extends BaseStructure
{
    public function getStructureType(): StructureEnum
    {
        return StructureType::Interface;
    }

    public function getProperties(): array
    {
        return [];
    }
}