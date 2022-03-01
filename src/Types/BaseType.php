<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

abstract class BaseType implements Type
{
    /**
     * @codeCoverageIgnore
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function isBuiltin(): bool
    {
        return true;
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function accepts(Type|string $type): bool
    {
        if ($type instanceof Type) {
            return $type instanceof static
                || $type->getName() === $this->getName()
                || is_subclass_of($this->getName(), $type->getName());
        }

        return $this->getName() === $type || is_subclass_of($this->getName(), $type);
    }
}