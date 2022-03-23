<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;

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

    public function isPrimitive(): bool
    {
        return true;
    }

    public function accepts(Type|string $type): bool
    {
        if (! ($type instanceof Type)) {
            $type = Inspector::getInstance()->types()->make($type);
        }

        if ($type instanceof NullableType) {
            $type = $type->getBaseType();
        }

        return $type instanceof static || $type->getName() === $this->getName();
    }
}