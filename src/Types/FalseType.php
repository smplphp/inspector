<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class FalseType extends BaseType
{
    public function getName(): string
    {
        return 'false';
    }

    public function matches(mixed $value): bool
    {
        return $value === false;
    }

    public function accepts(Type|string $type): bool
    {
        if ($type === 'mixed' || $type instanceof MixedType) {
            return true;
        }

        return ($type instanceof Type ? $type->getName() : $type) === $this->getName();
    }

    public function isNullable(): bool
    {
        return false;
    }
}