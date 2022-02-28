<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class StringType extends BaseType
{
    public function getName(): string
    {
        return 'string';
    }

    public function matches(mixed $value): bool
    {
        return is_string($value);
    }
}