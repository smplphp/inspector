<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

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
}