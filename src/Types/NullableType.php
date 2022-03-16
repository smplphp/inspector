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

    public function getBaseType(): Type
    {
        return $this->baseType;
    }

    public function accepts(Type|string $type): bool
    {
        if ($type instanceof NullableType) {
            return true;
        }

        $typeName = is_string($type) ? $type : $type->getName();

        /*
         * This should match all variations of:
         *
         *      null
         *      ?type
         *      type|null
         *      null|type
         *      type1|null|type2
         *
         */
        return $typeName === 'null'
            || str_starts_with($typeName, '?')
            || str_starts_with($typeName, 'null|')
            || str_ends_with($typeName, '|null')
            || str_contains($typeName, '|null|');
    }
}