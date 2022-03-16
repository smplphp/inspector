<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Stringable;

class StringType extends BaseType
{
    public function getName(): string
    {
        return 'string';
    }

    public function matches(mixed $value): bool
    {
        return is_string($value);
    }

    public function accepts(Type|string $type): bool
    {
        if ($type instanceof ClassType) {
            return $type->getName() === Stringable::class
                || is_subclass_of($type->getName(), Stringable::class);
        }

        return $type === Stringable::class || parent::accepts($type);
    }
}