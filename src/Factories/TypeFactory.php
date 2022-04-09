<?php

declare(strict_types=1);

namespace Smpl\Inspector\Factories;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Smpl\Inspector\Contracts;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Exceptions\TypeException;
use Smpl\Inspector\Support\MapperHelper;
use Smpl\Inspector\Types;

class TypeFactory implements Contracts\TypeFactory
{
    private static self $instance;

    public static function getInstance(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

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
     * @var \Smpl\Inspector\Types\SelfType[]
     */
    private array $selfTypes = [];

    /**
     * @var \Smpl\Inspector\Types\StaticType[]
     */
    private array $staticTypes = [];

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

    private function __construct()
    {
        // This method is intentionally empty
    }

    private function __clone()
    {
        // This method is intentionally empty
    }

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
     *
     * @infection-ignore-all
     */
    private function getTypesFromArray(array $types): array
    {
        return array_filter(array_map(function (ReflectionType|Contracts\Type|string $type): Contracts\Type {
            return $type instanceof Contracts\Type ? $type : $this->make($type);
        }, $types));
    }

    /**
     * @param array<\ReflectionType|\Smpl\Inspector\Contracts\Type|string> $types
     *
     * @return bool
     */
    private function pullNullFromArray(array &$types): bool
    {
        foreach ($types as $key => $type) {
            if ($type === 'null') {
                unset($types[$key]);
                return true;
            }
        }

        return false;
    }

    /** @infection-ignore-all */
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
        // @codeCoverageIgnoreStart
        if ($typeObject === null) {
            throw TypeException::invalid($type->__toString());
        }

        if ($type->allowsNull() && $typeObject->isNullable()) {
            return $this->makeNullable($typeObject);
        }
        // @codeCoverageIgnoreEnd

        return $typeObject;
    }

    public function makeSelf(ReflectionType|Type|string $self): Type
    {
        /** @infection-ignore-all */
        if (! ($self instanceof Type)) {
            $self = $this->make($self);
        }

        if (! ($self instanceof Types\ClassType)) {
            throw TypeException::invalidSelf();
        }

        $selfName = $self->getName();

        if (! isset($this->selfTypes[$selfName])) {
            $this->selfTypes[$selfName] = new Types\SelfType($self);
        }

        return $this->selfTypes[$selfName];
    }

    public function makeStatic(ReflectionType|Type|string $static): Type
    {
        /** @infection-ignore-all */
        if (! ($static instanceof Type)) {
            $static = $this->make($static);
        }

        if (! ($static instanceof Types\ClassType)) {
            throw TypeException::invalidStatic();
        }

        $staticName = $static->getName();

        if (! isset($this->staticTypes[$staticName])) {
            $this->staticTypes[$staticName] = new Types\StaticType($static);
        }

        return $this->staticTypes[$staticName];
    }

    public function makeNullable(ReflectionType|Contracts\Type|string $type): Types\NullableType
    {
        if ($type instanceof Types\NullableType) {
            return $type;
        }

        $baseType     = $type instanceof Contracts\Type ? $type : $this->make($type);
        $baseTypeName = $baseType->getName();

        if (! $baseType->isNullable()) {
            throw TypeException::invalidNullable($baseTypeName);
        }


        if (! isset($this->nullableTypes[$baseTypeName])) {
            $this->nullableTypes[$baseTypeName] = new Types\NullableType($baseType);
        }

        return $this->nullableTypes[$baseTypeName];
    }

    public function makeUnion(array $types): Types\UnionType|Types\NullableType
    {
        $nullable = $this->pullNullFromArray($types);
        $types    = $this->getTypesFromArray($types);
        /** @infection-ignore-all */
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
            /** @infection-ignore-all */
            throw TypeException::invalidUnion(implode(
                Contracts\Type::UNION_SEPARATOR,
                array_map(static fn(Contracts\Type $type) => $type->getName(), $types)
            ));
        }

        if (isset($this->unionTypes[$unionType->getName()])) {
            return $nullable
                ? $this->makeNullable($this->unionTypes[$unionType->getName()])
                : $this->unionTypes[$unionType->getName()];
        }

        $this->unionTypes[$unionType->getName()] = $unionType;

        /** @infection-ignore-all */
        return $nullable ? $this->makeNullable($unionType) : $unionType;
    }

    public function makeIntersection(array $types): Types\IntersectionType
    {
        $types = $this->getTypesFromArray($types);
        /** @infection-ignore-all */
        self::sortTypesByName($types);

        $onlyClassTypes = empty(array_filter($types, static function (Contracts\Type $type) {
            return ! ($type instanceof Types\ClassType);
        }));

        if (! $onlyClassTypes) {
            /** @infection-ignore-all */
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

        /** @infection-ignore-all */
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
        if (MapperHelper::isValidClass($typeName)) {
            return $this->createClassType($typeName);
        }

        return match ($typeName) {
            'false'    => new Types\FalseType(),
            'array'    => new Types\ArrayType(),
            'bool'     => new Types\BoolType(),
            'float'    => new Types\FloatType(),
            'int'      => new Types\IntType(),
            'iterable' => new Types\IterableType(),
            'object'   => new Types\ObjectType(),
            'string'   => new Types\StringType(),
            'void'     => new Types\VoidType(),
            'mixed'    => new Types\MixedType(),
            default    => throw TypeException::invalid($typeName)
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
                throw TypeException::invalidType($type);
            }

            return $this->makeNullable($this->makeBaseType(substr($type, 1)));
        }

        if (str_contains($type, Contracts\Type::UNION_SEPARATOR)) {
            return $this->makeUnion(explode(Contracts\Type::UNION_SEPARATOR, $type));
        }

        if (str_contains($type, Contracts\Type::INTERSECTION_SEPARATOR)) {
            $intersectionTypes = explode(Contracts\Type::INTERSECTION_SEPARATOR, $type);
            /**
             * @var list<class-string> $intersectionTypes
             */
            return $this->makeIntersection($intersectionTypes);
        }

        return $this->makeBaseType($type);
    }
}