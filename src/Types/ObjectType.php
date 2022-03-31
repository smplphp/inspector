<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\MapperHelper;

class ObjectType extends BaseType
{
    public function getName(): string
    {
        return 'object';
    }

    public function matches(mixed $value): bool
    {
        return is_object($value);
    }

    public function accepts(Type|string $type): bool
    {
        return parent::accepts($type)
            || $type instanceof ClassType
            || (is_string($type) && MapperHelper::isValidClass($type));
    }

    public function isPrimitive(): bool
    {
        return false;
    }
}