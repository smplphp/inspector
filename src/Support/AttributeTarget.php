<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

enum AttributeTarget: int
{
    /**
     * Marks that attribute declaration is allowed only in classes.
     *
     * @see \Attribute::TARGET_CLASS
     */
    case Structure = 1;

    /**
     * Marks that attribute declaration is allowed only in functions.
     *
     * @see \Attribute::TARGET_FUNCTION
     */
    case Function = 2;

    /**
     * Marks that attribute declaration is allowed only in class methods.
     *
     * @see \Attribute::TARGET_METHOD
     */
    case Method = 4;

    /**
     * Marks that attribute declaration is allowed only in class properties.
     *
     * @see \Attribute::TARGET_PROPERTY
     */
    case Property = 8;

    /**
     * Marks that attribute declaration is allowed only in class constants.
     *
     * @see \Attribute::TARGET_CLASS_CONSTANT
     */
    case Constant = 16;

    /**
     * Marks that attribute declaration is allowed only in function or method parameters.
     *
     * @see \Attribute::TARGET_PARAMETER
     */
    case Parameter = 32;

    /**
     * Marks that attribute declaration is allowed anywhere.
     *
     * @see \Attribute::TARGET_ALL
     */
    case All = 63;

    /**
     * @param int $flags
     *
     * @return \Smpl\Inspector\Support\AttributeTarget[]
     */
    public static function for(int $flags): array
    {
        $targets = [];

        foreach (self::cases() as $target) {
            if ($target !== self::All && $target->value & $flags) {
                $targets[] = $target;
            }
        }

        return $targets;
    }
}