<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class ClassType implements Type
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->className;
    }

    public function matches(mixed $value): bool
    {
        return ($value instanceof $this->className) || ($allowNull && $value === null);
    }

    public function isBuiltin(): bool
    {
        return false;
    }
}