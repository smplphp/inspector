<?php

declare(strict_types=1);

namespace Smpl\Inspector\Factories;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Smpl\Inspector\Contracts;
use Smpl\Inspector\Exceptions\TypeException;
use Smpl\Inspector\Types;

class TypeFactory implements Contracts\TypeFactory
{
    /**
     * @param ReflectionNamedType[]|\Smpl\Inspector\Contracts\Type[] $types
     *
     * @return void
     */
    private static function sortTypesByName(array &$types): void
    {
        usort(
            $types,
            static function (ReflectionNamedType|Contracts\Type $a, ReflectionNamedType|Contracts\Type $b): int {
                return strcmp($a->getName(), $b->getName());
            }
        );
    }

    /**
     * @var \Smpl\Inspector\Contracts\Type[]
     */
    private array $baseTypes = [];

    /**
     * @var \Smpl\Inspector\Types\UnionType[]
     */
    private array $unionTypes = [];

    /**
     * @var \Smpl\Inspector\Types\IntersectionType[]
     */
    private array $intersectionTypes = [];

    /**
     * @var \Smpl\Inspector\Types\NullableType[]
     */
    private array $nullableTypes = [];

    /**
     * Create a new class type instance.
     *
     * @param class-string $typeName
     *
     * @return \Smpl\Inspector\Types\ClassType
     */
    private function createClassType(string $typeName): Types\ClassType
    {
        return new Types\ClassType($typeName);
    }

    /**
     * @param array<\ReflectionType|\Smpl\Inspector\Contracts\Type|string> $types
     *
     * @return \Smpl\Inspector\Contracts\Type[]
     *
     * @throws \Smpl\Inspector\Exceptions\TypeException
     */
    private function getTypesFromArray(array $types): array
    {
        return array_filter(array_map(function (ReflectionType|Contracts\Type|string $type): Contracts\Type {
            return $type instanceof Contracts\Type ? $type : $this->make($type);
        }, $types));
    }

    public function make(ReflectionType|string $type): Contracts\Type
    {
        if (! ($type instanceof ReflectionType)) {
            return $this->makeTypeFromString($type);
        }

        $typeObject = null;

        if ($type instanceof ReflectionNamedType) {
            $typeObject = $this->makeNamedType($type);
        } else if ($type instanceof ReflectionUnionType) {
            $typeObject = $this->makeUnion($type->getTypes());
        } else if ($type instanceof ReflectionIntersectionType) {
            $typeObject = $this->makeIntersection($type->getTypes());
        }

        // If this happens there's a severe issue somewhere
        if ($typeObject === null) {
            // @codeCoverageIgnoreStart
            throw TypeException::invalid($type->__toString());
            // @codeCoverageIgnoreEnd
        }

        if (! ($typeObject instanceof Types\MixedType) && ! ($typeObject instanceof Types\VoidType) && $type->allowsNull()) {
            return $this->makeNullable($typeObject);
        }

        return $typeObject;
    }

    public function makeNullable(ReflectionType|Contracts\Type|string $type): Types\NullableType
    {
        if ($type instanceof Types\NullableType) {
            return $type;
        }

        $baseType     = $type instanceof Contracts\Type ? $type : $this->make($type);
        $baseTypeName = $baseType->getName();

        if (! isset($this->nullableTypes[$baseTypeName])) {
            $this->nullableTypes[$baseTypeName] = new Types\NullableType($baseType);
        }

        return $this->nullableTypes[$baseTypeName];
    }

    public function makeUnion(array $types): Types\UnionType
    {
        $types = $this->getTypesFromArray($types);
        self::sortTypesByName($types);
        $unionType = new Types\UnionType(...$types);

        $validUnionTypes = empty(array_filter($types, static function (Contracts\Type $type) {
            return $type instanceof Types\VoidType
                || $type instanceof Types\MixedType
                || $type instanceof Types\NullableType
                || $type instanceof Types\UnionType
                || $type instanceof Types\IntersectionType;
        }));

        if (! $validUnionTypes) {
            throw TypeException::invalidUnion(implode(
                Contracts\Type::UNION_SEPARATOR,
                array_map(static fn(Contracts\Type $type) => $type->getName(), $types)
            ));
        }

        if (isset($this->unionTypes[$unionType->getName()])) {
            return $this->unionTypes[$unionType->getName()];
        }

        $this->unionTypes[$unionType->getName()] = $unionType;

        return $unionType;
    }

    public function makeIntersection(array $types): Types\IntersectionType
    {
        $types = $this->getTypesFromArray($types);
        self::sortTypesByName($types);

        $onlyClassTypes = empty(array_filter($types, static function (Contracts\Type $type) {
            return ! ($type instanceof Types\ClassType);
        }));

        if (! $onlyClassTypes) {
            throw TypeException::invalidIntersection(implode(
                Contracts\Type::INTERSECTION_SEPARATOR,
                array_map(static fn(Contracts\Type $type) => $type->getName(), $types)
            ));
        }

        $intersectionType = new Types\IntersectionType(...$types);

        if (isset($this->intersectionTypes[$intersectionType->getName()])) {
            return $this->intersectionTypes[$intersectionType->getName()];
        }

        $this->intersectionTypes[$intersectionType->getName()] = $intersectionType;

        return $intersectionType;
    }

    private function makeBaseType(string $typeName): Contracts\Type
    {
        if (! isset($this->baseTypes[$typeName])) {
            $this->baseTypes[$typeName] = $this->createBaseType($typeName);
        }

        return $this->baseTypes[$typeName];
    }

    private function makeNamedType(ReflectionNamedType $reflectionType): Contracts\Type
    {
        $nullable = $reflectionType->allowsNull();
        $baseType = $this->makeBaseType($reflectionType->getName());

        if ($nullable && ($baseType instanceof Types\MixedType || $baseType instanceof Types\VoidType)) {
            $nullable = false;
        }

        return $nullable ? $this->makeNullable($baseType) : $baseType;
    }

    /**
     * @param string|class-string $typeName
     *
     * @return \Smpl\Inspector\Contracts\Type
     *
     * @throws \Smpl\Inspector\Exceptions\TypeException
     */
    private function createBaseType(string $typeName): Contracts\Type
    {
        if (StructureFactory::isValidClass($typeName)) {
            return $this->createClassType($typeName);
        }

        return match ($typeName) {
            'array'    => new Types\ArrayType(),
            'bool'     => new Types\BoolType(),
            'float'    => new Types\FloatType(),
            'int'      => new Types\IntType(),
            'iterable' => new Types\IterableType(),
            'object'   => new Types\ObjectType(),
            'string'   => new Types\StringType(),
            'void'     => new Types\VoidType(),
            'mixed'    => new Types\MixedType(),
            default    => throw TypeException::invalidBase($typeName)
        };
    }

    private function makeTypeFromString(string $type): Contracts\Type
    {
        if ($type !== 'void' && str_contains($type, 'void')) {
            throw TypeException::invalidVoid();
        }

        if ($type !== 'mixed' && str_contains($type, 'mixed')) {
            throw TypeException::invalidMixed();
        }

        if (str_starts_with($type, Contracts\Type::NULLABLE_CHARACTER)) {
            if (str_contains($type, Contracts\Type::UNION_SEPARATOR) || str_contains($type, Contracts\Type::INTERSECTION_SEPARATOR)) {
                throw TypeException::invalidNullable();
            }

            return $this->makeNullable($this->makeBaseType(substr($type, 1)));
        }

        if (str_contains($type, Contracts\Type::UNION_SEPARATOR)) {
            return $this->makeUnion(explode(Contracts\Type::UNION_SEPARATOR, $type));
        }

        if (str_contains($type, Contracts\Type::INTERSECTION_SEPARATOR)) {
            return $this->makeIntersection(explode(Contracts\Type::INTERSECTION_SEPARATOR, $type));
        }

        return $this->makeBaseType($type);
    }
}