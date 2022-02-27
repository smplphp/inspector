<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

enum StructureType
{
    case Default;
    case Interface;
    case Trait;
    case Enum;

    public function isInstantiable(): bool
    {
        return match ($this) {
            self::Default => true,
            default       => false
        };
    }
}