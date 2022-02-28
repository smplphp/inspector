<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class NullableType extends BaseType
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
        return $this->baseType->isBuiltin();
    }
}