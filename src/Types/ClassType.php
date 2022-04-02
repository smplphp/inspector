<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\TypeFactory;

class ClassType extends BaseType
{
    /**
     * @var class-string
     */
    private string $className;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return class-string
     */
    public function getName(): string
    {
        return $this->className;
    }

    public function matches(mixed $value): bool
    {
        if (is_object($value)) {
            return ($value instanceof $this->className);
        }

        if (! is_string($value)) {
            return false;
        }

        return $value === $this->className || is_subclass_of($value, $this->className);
    }

    public function isPrimitive(): bool
    {
        return false;
    }

    public function isBuiltin(): bool
    {
        return false;
    }

    public function accepts(Type|string $type): bool
    {
        if (! ($type instanceof Type)) {
            $type = TypeFactory::getInstance()->make($type);
        }

        if ($type instanceof static) {
            return $this->matches($type->getName());
        }

        return parent::accepts($type);
    }
}