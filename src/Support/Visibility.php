<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

use ReflectionMethod;
use ReflectionProperty;

enum Visibility
{
    case Public;
    case Protected;
    case Private;

    public static function getFromReflection(ReflectionProperty|ReflectionMethod $reflection): Visibility
    {
        if ($reflection->isProtected()) {
            return Visibility::Protected;
        }

        if ($reflection->isPrivate()) {
            return Visibility::Private;
        }

        return Visibility::Public;
    }
}