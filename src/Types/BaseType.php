<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\TypeFactory;

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
        if ($type === $this) {
            return true;
        }

        if (! ($type instanceof Type)) {
            $type = TypeFactory::getInstance()->make($type);
        }

        if ($type instanceof NullableType) {
            $type = $type->getBaseType();
        }

        if (
            $type::class === static::class
            && $type->isPrimitive()
            && ! method_exists($this, 'getSubtypes')
        ) {
            return true;
        }

        return $type->getName() === $this->getName();
    }
}