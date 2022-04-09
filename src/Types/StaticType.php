<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\TypeFactory;

class StaticType extends BaseType
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
        return 'static';
    }

    public function isPrimitive(): bool
    {
        return false;
    }

    public function isBuiltin(): bool
    {
        return false;
    }

    /** @infection-ignore-all  */
    public function getBaseType(): Type
    {
        return $this->baseType;
    }

    public function matches(mixed $value): bool
    {
        return $this->getBaseType()->matches($value);
    }

    public function accepts(Type|string $type): bool
    {
        return $this->getBaseType()->accepts($type);
    }
}