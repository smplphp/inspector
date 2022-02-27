<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class NullableType implements Type
{
    /**
     * @var \Smpl\Inspector\Contracts\Type
     */
    private Type $baseType;

    public function __construct(Type $baseType)
    {
        $this->baseType = $baseType;
    }

    public function getName(): string
    {
        return $this->baseType->getName() . '|null';
    }

    public function matches(mixed $value): bool
    {
        return $value === null || $this->baseType->matches($value);
    }

    public function isBuiltin(): bool
    {
        return true;
    }

    public function __toString()
    {
        return $this->getName();
    }
}