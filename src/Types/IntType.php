<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class IntType extends BaseType
{
    public function getName(): string
    {
        return 'int';
    }

    public function matches(mixed $value): bool
    {
        return is_int($value);
    }
}