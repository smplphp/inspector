<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Factories;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Smpl\Inspector\Contracts\TypeFactory;
use Smpl\Inspector\Exceptions\TypeException;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Tests\Fixtures\BasicInterface;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\SecondInterface;
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
use Smpl\Inspector\Types\VoidType;

/**
 * @group types
 * @group factories
 */
class TypeFactoryTest extends TestCase
{
    private TypeFactory $factory;

    protected function setUp(): void
    {
        $this->factory = Inspector::getInstance()->types();
    }

    /**
     * @test
     */
    public function creates_base_types_from_simple_strings(): void
    {
        self::assertInstanceOf(ArrayType::class, $this->factory->make('array'));
        self::assertInstanceOf(BoolType::class, $this->factory->make('bool'));
        self::assertInstanceOf(FloatType::class, $this->factory->make('float'));
        self::assertInstanceOf(IntType::class, $this->factory->make('int'));
        self::assertInstanceOf(IterableType::class, $this->factory->make('iterable'));
        self::assertInstanceOf(MixedType::class, $this->factory->make('mixed'));
        self::assertInstanceOf(ObjectType::class, $this->factory->make('object'));
        self::assertInstanceOf(StringType::class, $this->factory->make('string'));
        self::assertInstanceOf(VoidType::class, $this->factory->make('void'));
    }

    /**
     * @test
     */
    public function creates_only_one_instance_of_base_types(): void
    {
        self::assertSame($this->factory->make('array'), $this->factory->make('array'));
        self::assertSame($this->factory->make('bool'), $this->factory->make('bool'));
        self::assertSame($this->factory->make('float'), $this->factory->make('float'));
        self::assertSame($this->factory->make('int'), $this->factory->make('int'));
        self::assertSame($this->factory->make('iterable'), $this->factory->make('iterable'));
        self::assertSame($this->factory->make('mixed'), $this->factory->make('mixed'));
        self::assertSame($this->factory->make('object'), $this->factory->make('object'));
        self::assertSame($this->factory->make('string'), $this->factory->make('string'));
        self::assertSame($this->factory->make('void'), $this->factory->make('void'));
    }

    /**
     * @test
     */
    public function creates_class_types_from_class_names(): void
    {
        self::assertInstanceOf(ClassType::class, $this->factory->make(BasicInterface::class));
    }

    /**
     * @test
     */
    public function throws_an_exception_for_unknown_types(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Cannot create a type for the provided value \'invalid\'');

        $this->factory->make('invalid');
    }

    /**
     * @test
     */
    public function creates_base_types_from_nullable_strings(): void
    {
        $arrayType    = $this->factory->make('?array');
        $boolType     = $this->factory->make('?bool');
        $floatType    = $this->factory->make('?float');
        $intType      = $this->factory->make('?int');
        $iterableType = $this->factory->make('?iterable');
        $objectType   = $this->factory->make('?object');
        $stringType   = $this->factory->make('?string');

        self::assertInstanceOf(NullableType::class, $arrayType);
        self::assertInstanceOf(ArrayType::class, $arrayType->getBaseType());
        self::assertInstanceOf(NullableType::class, $boolType);
        self::assertInstanceOf(BoolType::class, $boolType->getBaseType());
        self::assertInstanceOf(NullableType::class, $floatType);
        self::assertInstanceOf(FloatType::class, $floatType->getBaseType());
        self::assertInstanceOf(NullableType::class, $intType);
        self::assertInstanceOf(IntType::class, $intType->getBaseType());
        self::assertInstanceOf(NullableType::class, $iterableType);
        self::assertInstanceOf(IterableType::class, $iterableType->getBaseType());
        self::assertInstanceOf(NullableType::class, $objectType);
        self::assertInstanceOf(ObjectType::class, $objectType->getBaseType());
        self::assertInstanceOf(NullableType::class, $stringType);
        self::assertInstanceOf(StringType::class, $stringType->getBaseType());
    }

    /**
     * @test
     */
    public function returns_nullable_type_when_attempting_to_wrap_in_another_nullable(): void
    {
        $nullableType = $this->factory->makeNullable('array');

        self::assertSame($nullableType, $this->factory->makeNullable($nullableType));
    }

    /**
     * @test
     */
    public function throws_an_exception_for_nullable_void_types(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Void types cannot be nullable, or part of a union type');

        $this->factory->make('?void');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_nullable_union_types(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Nullable types cannot include union or intersection types');

        $this->factory->make('?int|string');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_nullable_intersection_types(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Nullable types cannot include union or intersection types');

        $this->factory->make('?' . BasicInterface::class . '&' . ExampleClass::class);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_nullable_mixed_types(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Mixed types cannot be nullable, or part of a union type');

        $this->factory->make('?mixed');
    }

    /**
     * @test
     */
    public function creates_union_types_from_string(): void
    {
        $unionType = $this->factory->make('string|int');

        self::assertInstanceOf(UnionType::class, $unionType);
    }

    /**
     * @test
     */
    public function creates_only_one_instance_of_union_types(): void
    {
        self::assertSame($this->factory->make('string|int'), $this->factory->make('string|int'));
    }

    /**
     * @test
     */
    public function creates_nullable_union_types(): void
    {
        $type1 = $this->factory->make('string|int|null');
        $type2 = $this->factory->makeUnion(['string', 'float', 'null']);

        self::assertInstanceOf(NullableType::class, $type1);
        self::assertInstanceOf(NullableType::class, $type2);
    }

    /**
     * @test
     */
    public function union_subtypes_are_ordered_by_name_not_definition_order(): void
    {
        $unionType = $this->factory->makeUnion(['string', 'int']);
        $subtypes  = $unionType->getSubtypes();

        self::assertInstanceOf(IntType::class, $subtypes[0]);
        self::assertInstanceOf(StringType::class, $subtypes[1]);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_union_types_that_include_void(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Invalid union type \'string|void\'');

        $this->factory->makeUnion(['string', 'void']);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_union_types_that_include_mixed(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Invalid union type \'mixed|string\'');

        $this->factory->makeUnion(['string', 'mixed']);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_union_types_with_unknown_types(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Cannot create a type for the provided value \'invalid\'');

        $this->factory->makeUnion(['invalid', 'also-invalid']);
    }

    /**
     * @test
     */
    public function creates_intersection_types_from_string(): void
    {
        $intersectionType = $this->factory->make(BasicInterface::class . '&' . ExampleClass::class);

        self::assertInstanceOf(IntersectionType::class, $intersectionType);
    }

    /**
     * @test
     */
    public function creates_only_one_instance_of_intersection_types(): void
    {
        self::assertSame(
            $this->factory->make(BasicInterface::class . '&' . ExampleClass::class),
            $this->factory->make(BasicInterface::class . '&' . ExampleClass::class)
        );
    }

    /**
     * @test
     */
    public function intersection_subtypes_are_ordered_by_name_not_definition_order(): void
    {
        $unionType = $this->factory->makeIntersection([ExampleClass::class, BasicInterface::class]);
        $subtypes  = $unionType->getSubtypes();

        self::assertSame(BasicInterface::class, $subtypes[0]->getName());
        self::assertSame(ExampleClass::class, $subtypes[1]->getName());
    }

    /**
     * @test
     */
    public function throws_an_exception_for_intersection_types_that_include_none_classes(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Invalid intersection type \'' . BasicInterface::class . '&string\'');

        $this->factory->makeIntersection([BasicInterface::class, 'string']);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_intersection_types_with_unknown_types(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionMessage('Cannot create a type for the provided value \'invalid\'');

        $this->factory->makeIntersection(['invalid', 'also-invalid']);
    }

    /**
     * @test
     */
    public function creates_base_types_from_reflection(): void
    {
        $reflection = new ReflectionClass(ExampleClass::class);
        $property   = $reflection->getProperty('publicStringProperty');
        $type       = $this->factory->make($property->getType());

        self::assertInstanceOf(StringType::class, $type);
    }

    /**
     * @test
     */
    public function creates_nullable_types_from_reflection(): void
    {
        $reflection = new ReflectionClass(ExampleClass::class);
        $property   = $reflection->getProperty('nullablePrivateIntProperty');
        $type       = $this->factory->make($property->getType());

        self::assertInstanceOf(NullableType::class, $type);
        self::assertInstanceOf(IntType::class, $type->getBaseType());
    }

    /**
     * @test
     */
    public function creates_union_types_from_reflection(): void
    {
        $reflection = new ReflectionClass(ExampleClass::class);
        $property   = $reflection->getProperty('publicUnionType');
        $type       = $this->factory->make($property->getType());

        self::assertInstanceOf(UnionType::class, $type);

        $subtypes = $type->getSubtypes();

        self::assertInstanceOf(IntType::class, $subtypes[0]);
        self::assertInstanceOf(StringType::class, $subtypes[1]);
    }

    /**
     * @test
     */
    public function creates_intersection_types_from_reflection(): void
    {
        $reflection = new ReflectionClass(ExampleClass::class);
        $property   = $reflection->getProperty('publicIntersectionType');
        $type       = $this->factory->make($property->getType());

        self::assertInstanceOf(IntersectionType::class, $type);

        $subtypes = $type->getSubtypes();

        self::assertInstanceOf(ClassType::class, $subtypes[0]);
        self::assertEquals(BasicInterface::class, $subtypes[0]);
        self::assertInstanceOf(ClassType::class, $subtypes[1]);
        self::assertEquals(SecondInterface::class, $subtypes[1]);
    }

    /**
     * @test
     */
    public function creates_mixed_types_from_reflection_without_wrapping_in_a_nullable(): void
    {
        $reflection = new ReflectionClass(ExampleClass::class);
        $property   = $reflection->getProperty('protectedMixedProperty');
        $type       = $this->factory->make($property->getType());

        self::assertInstanceOf(MixedType::class, $type);
    }
}