<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class BoolType extends BaseType
{
    public function getName(): string
    {
        return 'bool';
    }

    public function matches(mixed $value): bool
    {
        return is_bool($value);
    }
}