<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class ArrayType extends BaseType
{
    public function getName(): string
    {
        return 'array';
    }

    public function matches(mixed $value): bool
    {
        return is_array($value);
    }
}