<?php

declare(strict_types=1);

namespace Smpl\Inspector\Structures;

use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Support\StructureType as StructureEnum;

class EnumStructure extends BaseStructure
{
    public function getStructureType(): StructureEnum
    {
        return StructureType::Enum;
    }

    public function getProperties(): array
    {
        return [];
    }
}