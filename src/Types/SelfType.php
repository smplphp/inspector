<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Contracts\WrapperType;
use Smpl\Inspector\Factories\TypeFactory;

class SelfType extends BaseType implements WrapperType
{
    /**
     * @var \Smpl\Inspector\Types\ClassType
     */
    private ClassType $baseType;

    public function __construct(ClassType $class)
    {
        $this->baseType = $class;
    }

    public function getName(): string
    {
        return 'self';
    }

    public function isPrimitive(): bool
    {
        return false;
    }

    public function isBuiltin(): bool
    {
        return false;
    }

    public function matches(mixed $value): bool
    {
        if (is_string($value)) {
            return $value === $this->getBaseType()->getName();
        }

        if (is_object($value)) {
            return $value::class === $this->getBaseType()->getName();
        }

        return false;
    }

    /** @infection-ignore-all  */
    public function getBaseType(): Type
    {
        return $this->baseType;
    }

    public function accepts(Type|string $type): bool
    {
        if (! ($type instanceof Type)) {
            $type = TypeFactory::getInstance()->make($type);
        }

        if ($type instanceof WrapperType) {
            $type = $type->getBaseType();
        }

        return $type instanceof SelfType || $type->getName() === $this->getBaseType()->getName();
    }
}