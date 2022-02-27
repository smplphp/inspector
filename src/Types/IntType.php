<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class IntType implements Type
{
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return 'int';
    }

    public function matches(mixed $value): bool
    {
        return is_int($value) || ($allowNull && $value === null);
    }

    public function isBuiltin(): bool
    {
        return true;
    }
}