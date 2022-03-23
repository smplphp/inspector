<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

/**
 * Structure Type Enum
 *
 * Represents the type of structures (classes) available within PHP.
 */
enum StructureType: string
{
    case Default = 'class';
    case Interface = 'interface';
    case Enum = 'enum';
    case Trait = 'trait';
    case Attribute = 'attribute';

    /**
     * Check if the structure type can extend classes.
     *
     * @return bool
     */
    public function canExtend(): bool
    {
        return match ($this) {
            self::Default, self::Interface => true,
            default                        => false
        };
    }

    /**
     * Check if the structure type can implement interfaces.
     *
     * @return bool
     */
    public function canImplement(): bool
    {
        return match ($this) {
            self::Default, self::Enum => true,
            default                   => false
        };
    }

    /**
     * Check if the structure type can have properties.
     *
     * @return bool
     */
    public function canHaveProperties(): bool
    {
        return match ($this) {
            self::Default, self::Trait => true,
            default                    => false
        };
    }

    /**
     * Check if the structure type can be instantiated.
     *
     * @return bool
     */
    public function canBeInstantiated(): bool
    {
        return match ($this) {
            self::Default => true,
            default       => false
        };
    }
}