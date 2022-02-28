<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

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
}