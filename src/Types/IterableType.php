<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class IterableType implements Type
{
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return 'iterable';
    }

    public function matches(mixed $value): bool
    {
        return is_iterable($value) || ($allowNull && $value === null);
    }

    public function isBuiltin(): bool
    {
        return true;
    }
}