<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class VoidType extends BaseType
{
    public function getName(): string
    {
        return 'void';
    }

    public function matches(mixed $value): bool
    {
        return $value === null;
    }
}