<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class FloatType extends BaseType
{
    public function getName(): string
    {
        return 'float';
    }

    public function matches(mixed $value): bool
    {
        return is_float($value);
    }
}