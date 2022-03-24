<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Traversable;

class IterableType extends BaseType
{
    public function getName(): string
    {
        return 'iterable';
    }

    public function matches(mixed $value): bool
    {
        return is_iterable($value);
    }

    public function accepts(Type|string $type): bool
    {
        if ($type instanceof ArrayType || $type === 'array') {
            return true;
        }

        if (is_string($type) && ($type === Traversable::class || is_subclass_of($type, Traversable::class))) {
            return true;
        }

        if ($type instanceof ClassType) {
            return $type->getName() === Traversable::class
                || is_subclass_of($type->getName(), Traversable::class);
        }

        return parent::accepts($type);
    }
}