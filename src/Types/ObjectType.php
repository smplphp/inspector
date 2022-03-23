<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\StructureFactory;

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
            || (is_string($type) && StructureFactory::isValidClass($type));
    }

    public function isPrimitive(): bool
    {
        return false;
    }
}