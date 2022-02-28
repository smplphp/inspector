<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class IterableType extends BaseType
{
    public function getName(): string
    {
        return 'iterable';
    }

    public function matches(mixed $value): bool
    {
        return is_iterable($value);
    }
}