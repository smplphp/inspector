<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class MixedType extends BaseType
{
    public function getName(): string
    {
        return 'mixed';
    }

    public function matches(mixed $value): bool
    {
        return true;
    }

    public function accepts(Type|string $type): bool
    {
        return ! ($type === 'never' || $type === 'void' || $type instanceof VoidType);
    }
}