<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

class ClassType extends BaseType
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function getName(): string
    {
        return $this->className;
    }

    public function matches(mixed $value): bool
    {
        if (is_object($value)) {
            return ($value instanceof $this->className);
        }

        return $value === $this->className || is_subclass_of($value, $this->className);
    }

    public function isBuiltin(): bool
    {
        return false;
    }
}