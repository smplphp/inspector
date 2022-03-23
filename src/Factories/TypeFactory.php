<?php

declare(strict_types=1);

namespace Smpl\Inspector\Factories;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;
use Smpl\Inspector\Contracts;
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

        if ($typeObject === null) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(sprintf('Unable to create type for \'%s\'', $type->__toString()));
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
            default    => new Types\MixedType()
        };
    }

    private function makeTypeFromString(string $type): Contracts\Type
    {
        if (str_starts_with($type, '&')) {
            $type = substr($type, 1);
        }

        if (str_starts_with($type, '?')) {
            return $this->makeNullable($this->makeBaseType(substr($type, 1)));
        }

        if (str_contains($type, '|')) {
            return $this->makeUnion(explode('|', $type));
        }

        if (str_contains($type, '&')) {
            return $this->makeIntersection(explode('&', $type));
        }

        return $this->makeBaseType($type);
    }
}