<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class MixedType implements Type
{
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return 'mixed';
    }

    public function matches(mixed $value): bool
    {
        return true;
    }

    public function isBuiltin(): bool
    {
        return true;
    }
}