<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class BoolType implements Type
{
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return 'bool';
    }

    public function matches(mixed $value): bool
    {
        return is_bool($value) || ($allowNull && $value === null);
    }

    public function isBuiltin(): bool
    {
        return true;
    }
}