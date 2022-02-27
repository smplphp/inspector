<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class StringType implements Type
{
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return 'string';
    }

    public function matches(mixed $value): bool
    {
        return is_string($value) || ($allowNull && $value === null);
    }

    public function isBuiltin(): bool
    {
        return true;
    }
}