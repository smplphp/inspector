<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class VoidType extends BaseType
{
    public function getName(): string
    {
        return 'void';
    }

    public function matches(mixed $value): bool
    {
        return false;
    }

    public function accepts(Type|string $type): bool
    {
        return $type === 'null'
            || $type === 'void'
            || $type === 'never'
            || $type instanceof self;
    }

    public function isNullable(): bool
    {
        return false;
    }
}