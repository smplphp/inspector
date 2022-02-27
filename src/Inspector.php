<?php

declare(strict_types=1);

namespace Smpl\Inspector;

use Attribute as CoreAttribute;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;
use Smpl\Inspector\Contracts\Attribute;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Elements\Metadata;
use Smpl\Inspector\Structures\ClassStructure;
use Smpl\Inspector\Structures\EnumStructure;
use Smpl\Inspector\Structures\InterfaceStructure;
use Smpl\Inspector\Structures\TraitStructure;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Types\ArrayType;
use Smpl\Inspector\Types\BoolType;
use Smpl\Inspector\Types\ClassType;
use Smpl\Inspector\Types\FloatType;
use Smpl\Inspector\Types\IntersectionType;
use Smpl\Inspector\Types\IntType;
use Smpl\Inspector\Types\IterableType;
use Smpl\Inspector\Types\MixedType;
use Smpl\Inspector\Types\NullableType;
use Smpl\Inspector\Types\ObjectType;
use Smpl\Inspector\Types\StringType;
use Smpl\Inspector\Types\UnionType;

final class Inspector
{
    private static self $instance;

    private static function orderTypes(array $types): array
    {
        usort(
            $types,
            static fn(ReflectionNamedType $a, ReflectionNamedType $b) => strcmp($a->getName(), $b->getName())
        );

        return $types;
    }

    private static function findStructureType(ReflectionClass $reflection): StructureType
    {
        if ($reflection->isInterface()) {
            return StructureType::Interface;
        }

        if ($reflection->isTrait()) {
            return StructureType::Trait;
        }

        if ($reflection->isEnum()) {
            return StructureType::Enum;
        }

        return StructureType::Default;
    }

    public static function getInstance(): Inspector
    {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private array $baseTypes = [];

    private array $unionTypes = [];

    private array $intersectionTypes = [];

    private array $nullableTypes = [];

    private array $structures = [];

    private array $attributes = [];

    private function createBaseType(string $typeName): Type
    {
        return match ($typeName) {
            'array'    => new ArrayType(),
            'bool'     => new BoolType(),
            'class'    => new ClassType($typeName),
            'float'    => new FloatType(),
            'int'      => new IntType(),
            'iterable' => new IterableType(),
            'mixed'    => new MixedType(),
            'object'   => new ObjectType(),
            'string'   => new StringType()
        };
    }

    public function makeAttribute(string $attributeName): Attribute
    {
        if (isset($this->attributes[$attributeName])) {
            return $this->attributes[$attributeName];
        }

        $reflection = new ReflectionClass($attributeName);
        $attribute  = $reflection->getAttributes(CoreAttribute::class)[0] ?? null;

        if ($attribute === null) {
            throw new RuntimeException(sprintf('Provided attribute \'$%s\' is not a valid attribute', $attributeName));
        }

        /** @noinspection PhpParamsInspection */
        return $this->attributes[$attributeName] = new Elements\Attribute($attribute->newInstance(), $attributeName);
    }

    public function makeType(ReflectionType $reflectionType): ?Type
    {
        if ($reflectionType instanceof ReflectionNamedType) {
            return $this->makeNamedType($reflectionType);
        }

        if ($reflectionType instanceof ReflectionUnionType) {
            $type = $this->makeUnionType($reflectionType);
        } else if ($reflectionType instanceof ReflectionIntersectionType) {
            $type = $this->makeIntersectionType($reflectionType);
        } else {
            return $this->makeBaseType('mixed');
        }

        if ($reflectionType->allowsNull()) {
            return $this->makeNullableType($type);
        }

        return $type;
    }

    public function makeStructure(string|ReflectionClass $className)
    {
        if ($className instanceof ReflectionClass) {
            $reflection = $className;
            $className  = $reflection->getName();
        } else {
            $reflection = new ReflectionClass($className);
        }

        if ($reflection->getAttributes(CoreAttribute::class)[0] ?? null) {
            throw new RuntimeException(sprintf(
                'Cannot create a structure for class \'%s\' because it is an attribute',
                $className
            ));
        }

        $type = $this->makeBaseType($className);

        if (! isset($this->structures[$className])) {
            $this->structures[$className] = match (self::findStructureType($reflection)) {
                StructureType::Default   => new ClassStructure($reflection, $type),
                StructureType::Enum      => new EnumStructure($reflection, $type),
                StructureType::Interface => new InterfaceStructure($reflection, $type),
                StructureType::Trait     => new TraitStructure($reflection, $type)
            };
        }

        return $this->structures[$className];
    }

    public function makeMetadata(ReflectionAttribute $attribute): Metadata
    {
        return new Metadata($attribute, $this->makeAttribute($attribute->getName()));
    }

    public function makeBaseType(string $typeName): Type
    {
        if (! isset($this->baseTypes[$typeName])) {
            $this->baseTypes[$typeName] = $this->createBaseType($typeName);
        }

        return $this->baseTypes[$typeName];
    }

    private function makeIntersectionType(ReflectionIntersectionType $reflectionType): IntersectionType
    {
        $childTypes       = self::orderTypes($reflectionType->getTypes());
        $intersectionType = new IntersectionType(...array_map($this->makeNamedType(...), $childTypes));

        if (isset($this->intersectionTypes[$intersectionType->getName()])) {
            return $this->intersectionTypes[$intersectionType->getName()];
        }

        $this->intersectionTypes[$intersectionType->getName()] = $intersectionType;

        return $intersectionType;
    }

    private function makeNamedType(ReflectionNamedType $reflectionType): Type
    {
        $nullable = $reflectionType->allowsNull();
        $typeName = $nullable ? substr($reflectionType->getName(), 1) : $reflectionType->getName();
        $baseType = $this->makeBaseType($typeName);

        return $nullable ? $this->makeNullableType($baseType) : $baseType;
    }

    private function makeNullableType(Type $baseType): NullableType
    {
        $baseTypeName = $baseType->getName();

        if (! isset($this->nullableTypes[$baseTypeName])) {
            $this->nullableTypes[$baseTypeName] = new NullableType($baseType);
        }

        return $this->nullableTypes[$baseTypeName];
    }

    private function makeUnionType(ReflectionUnionType $reflectionType): UnionType
    {
        $childTypes = self::orderTypes($reflectionType->getTypes());
        $unionType  = new UnionType(...array_map($this->makeNamedType(...), $childTypes));

        if (isset($this->unionTypes[$unionType->getName()])) {
            return $this->unionTypes[$unionType->getName()];
        }

        $this->unionTypes[$unionType->getName()] = $unionType;

        return $unionType;
    }
}