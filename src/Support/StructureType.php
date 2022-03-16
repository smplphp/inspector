<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

enum StructureType: string
{
    case Default = 'class';
    case Interface = 'interface';
    case Enum = 'enum';
    case Trait = 'trait';
    case Attribute = 'attribute';

    public function canExtend(): bool
    {
        return match ($this) {
            self::Default, self::Interface => true,
            default                        => false
        };
    }

    public function canImplement(): bool
    {
        return match ($this) {
            self::Default, self::Enum => true,
            default                   => false
        };
    }

    public function canHaveProperties(): bool
    {
        return match ($this) {
            self::Default, self::Trait => true,
            default                    => false
        };
    }

    public function canBeInstantiated(): bool
    {
        return match ($this) {
            self::Default => true,
            default       => false
        };
    }
}