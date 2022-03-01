<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Types;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Types;

/**
 * @group tests
 */
class TypeAcceptsTest extends TestCase
{
    /**
     * @test
     */
    public function array_types_accepts_correctly(): void
    {
        $type = new Types\ArrayType();

        self::assertTrue($type->accepts('array'));
        self::assertTrue($type->accepts(new Types\ArrayType()));
        self::assertFalse($type->accepts('string'));
        self::assertFalse($type->accepts(new Types\StringType()));
    }

    /**
     * @test
     */
    public function bool_types_accepts_correctly(): void
    {
        $type = new Types\BoolType();

        self::assertTrue($type->accepts('bool'));
        self::assertTrue($type->accepts(new Types\BoolType()));
        self::assertFalse($type->accepts('string'));
        self::assertFalse($type->accepts(new Types\StringType()));
    }

    /**
     * @test
     */
    public function class_types_accepts_correctly(): void
    {
        $type = new Types\ClassType(Types\StringType::class);

        self::assertTrue($type->accepts(Types\StringType::class));
        self::assertTrue($type->accepts(new Types\ClassType(Types\StringType::class)));
        self::assertFalse($type->accepts('string'));
        self::assertFalse($type->accepts(new Types\IntType()));
    }

    /**
     * @test
     */
    public function float_types_accepts_correctly(): void
    {
        $type = new Types\FloatType();

        self::assertTrue($type->accepts('float'));
        self::assertTrue($type->accepts(new Types\FloatType()));
        self::assertFalse($type->accepts('string'));
        self::assertFalse($type->accepts(new Types\StringType()));
    }

    /**
     * @test
     */
    public function intersection_types_accepts_correctly(): void
    {
        $type = new Types\IntersectionType(
            new Types\ClassType(Type::class),
            new Types\ClassType(Types\StringType::class)
        );

        self::assertTrue($type->accepts(Type::class . '&' . Types\StringType::class));
        self::assertTrue($type->accepts(new Types\ClassType(Types\StringType::class)));
        self::assertTrue($type->accepts(Types\StringType::class));
        self::assertFalse($type->accepts('array'));
        self::assertFalse($type->accepts(new Types\ArrayType()));
    }

    /**
     * @test
     */
    public function int_types_accepts_correctly(): void
    {
        $type = new Types\IntType();

        self::assertTrue($type->accepts('int'));
        self::assertTrue($type->accepts(new Types\IntType()));
        self::assertFalse($type->accepts('string'));
        self::assertFalse($type->accepts(new Types\StringType()));
    }

    /**
     * @test
     */
    public function iterable_types_accepts_correctly(): void
    {
        $type = new Types\IterableType();

        self::assertTrue($type->accepts('iterable'));
        self::assertTrue($type->accepts('array'));
        self::assertTrue($type->accepts(\Traversable::class));
        self::assertTrue($type->accepts(\Iterator::class));
        self::assertTrue($type->accepts(\IteratorAggregate::class));
        self::assertTrue($type->accepts(StructurePropertyCollection::class));
        self::assertTrue($type->accepts(new Types\IterableType()));
        self::assertTrue($type->accepts(new Types\ArrayType()));
        self::assertTrue($type->accepts(new Types\ClassType(\Traversable::class)));
        self::assertTrue($type->accepts(new Types\ClassType(\Iterator::class)));
        self::assertTrue($type->accepts(new Types\ClassType(\IteratorAggregate::class)));
        self::assertTrue($type->accepts(new Types\ClassType(StructurePropertyCollection::class)));

        self::assertFalse($type->accepts('int'));
        self::assertFalse($type->accepts(new Types\StringType()));
    }

    /**
     * @test
     */
    public function mixed_types_accepts_correctly(): void
    {
        $type = new Types\MixedType();

        self::assertTrue($type->accepts('mixed'));
        self::assertTrue($type->accepts('string'));
        self::assertTrue($type->accepts('array'));
        self::assertTrue($type->accepts('int'));
        self::assertTrue($type->accepts(new Types\MixedType()));
        self::assertTrue($type->accepts(new Types\StringType()));
        self::assertTrue($type->accepts(new Types\ArrayType()));
        self::assertTrue($type->accepts(new Types\IntType()));

        self::assertFalse($type->accepts('void'));
        self::assertFalse($type->accepts(new Types\VoidType()));
    }

    /**
     * @test
     */
    public function nullable_types_accepts_correctly(): void
    {
        $type = new Types\NullableType(new Types\StringType());

        self::assertTrue($type->accepts('null'));
        self::assertTrue($type->accepts('?string'));
        self::assertTrue($type->accepts('string|int|null'));
        self::assertTrue($type->accepts(new Types\NullableType(new Types\ArrayType())));

        self::assertFalse($type->accepts('int'));
        self::assertFalse($type->accepts(new Types\IntType()));
    }

    /**
     * @test
     */
    public function object_types_accepts_correctly(): void
    {
        $type = new Types\ObjectType();

        self::assertTrue($type->accepts('object'));
        self::assertTrue($type->accepts(new Types\ObjectType()));
        self::assertTrue($type->accepts(Types\ObjectType::class));
        self::assertTrue($type->accepts(Types\ClassType::class));
        self::assertTrue($type->accepts(new Types\ClassType(Types\ObjectType::class)));

        self::assertFalse($type->accepts('string'));
        self::assertFalse($type->accepts(new Types\StringType()));
    }

    /**
     * @test
     */
    public function string_types_accepts_correctly(): void
    {
        $type = new Types\StringType();

        self::assertTrue($type->accepts('string'));
        self::assertTrue($type->accepts(new Types\StringType()));
        self::assertTrue($type->accepts(\Stringable::class));
        self::assertTrue($type->accepts(new Types\ClassType(\Stringable::class)));

        self::assertFalse($type->accepts('array'));
        self::assertFalse($type->accepts(new Types\ArrayType()));
    }

    /**
     * @test
     */
    public function union_types_accepts_correctly(): void
    {
        $type = new Types\UnionType(new Types\StringType(), new Types\IntType());

        self::assertTrue($type->accepts('string|int'));
        self::assertTrue($type->accepts(new Types\UnionType(new Types\IntType(), new Types\StringType())));
        self::assertTrue($type->accepts('string'));
        self::assertTrue($type->accepts('string|float'));
        self::assertTrue($type->accepts(new Types\StringType()));

        self::assertFalse($type->accepts('float'));
        self::assertFalse($type->accepts(new Types\FloatType()));
    }

    /**
     * @test
     */
    public function void_types_accepts_correctly(): void
    {
        $type = new Types\VoidType();

        self::assertTrue($type->accepts('void'));
        self::assertTrue($type->accepts('null'));
        self::assertTrue($type->accepts(new Types\VoidType()));

        self::assertFalse($type->accepts('array'));
        self::assertFalse($type->accepts('mixed'));
        self::assertFalse($type->accepts('int'));
        self::assertFalse($type->accepts(new Types\ArrayType()));
        self::assertFalse($type->accepts(new Types\MixedType()));
        self::assertFalse($type->accepts(new Types\NullableType(new Types\StringType())));
    }
}