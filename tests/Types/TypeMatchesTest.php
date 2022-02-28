<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Types;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\TypeFactory;
use Smpl\Inspector\Types;
use stdClass;

/**
 * @group tests
 */
class TypeMatchesTest extends TestCase
{
    /**
     * @test
     */
    public function array_types_matches_correctly(): void
    {
        $type = new Types\ArrayType();

        self::assertTrue($type->matches([]));
        self::assertFalse($type->matches('[]'));
    }

    /**
     * @test
     */
    public function bool_types_matches_correctly(): void
    {
        $type = new Types\BoolType();

        self::assertTrue($type->matches(false));
        self::assertFalse($type->matches('false'));
    }

    /**
     * @test
     */
    public function class_types_matches_objects_correctly(): void
    {
        $type = new Types\ClassType(Types\StringType::class);

        self::assertTrue($type->matches(new Types\StringType()));
        self::assertFalse($type->matches(new Types\IntType()));
    }

    /**
     * @test
     */
    public function class_types_matches_class_names_correctly(): void
    {
        $type = new Types\ClassType(Types\StringType::class);

        self::assertTrue($type->matches(Types\StringType::class));
        self::assertFalse($type->matches(Types\IntType::class));
    }

    /**
     * @test
     */
    public function class_types_matches_inheritance_correctly(): void
    {
        $type = new Types\ClassType(Type::class);

        self::assertTrue($type->matches(new Types\StringType()));
        self::assertTrue($type->matches(Types\StringType::class));
        self::assertFalse($type->matches(new TypeFactory()));
        self::assertFalse($type->matches(TypeFactory::class));
    }

    /**
     * @test
     */
    public function float_types_matches_correctly(): void
    {
        $type = new Types\FloatType();

        self::assertTrue($type->matches(1.0));
        self::assertFalse($type->matches('1.0'));
    }

    /**
     * @test
     */
    public function intersection_types_matches_correctly(): void
    {
        $type = new Types\IntersectionType(
            new Types\ClassType(Type::class),
            new Types\ClassType(Types\StringType::class)
        );

        self::assertTrue($type->matches(new Types\StringType()));
        self::assertFalse($type->matches(new Types\IntType()));
    }

    /**
     * @test
     */
    public function int_types_matches_correctly(): void
    {
        $type = new Types\IntType();

        self::assertTrue($type->matches(1));
        self::assertFalse($type->matches(1.0));
    }

    /**
     * @test
     */
    public function iterable_types_matches_correctly(): void
    {
        $type = new Types\IterableType();

        self::assertTrue($type->matches([]));
        self::assertTrue($type->matches(new ArrayIterator([])));
        self::assertFalse($type->matches('[]'));
    }

    /**
     * @test
     */
    public function mixed_types_matches_correctly(): void
    {
        $type = new Types\MixedType();

        self::assertTrue($type->matches(1));
        self::assertTrue($type->matches(1.0));
        self::assertTrue($type->matches('string'));
        self::assertTrue($type->matches(false));
        self::assertTrue($type->matches(null));
        self::assertTrue($type->matches(new Types\StringType()));
    }

    /**
     * @test
     */
    public function nullable_types_matches_correctly(): void
    {
        $type = new Types\NullableType(new Types\StringType());

        self::assertTrue($type->matches('string'));
        self::assertTrue($type->matches(null));
        self::assertFalse($type->matches(1));
    }

    /**
     * @test
     */
    public function object_types_matches_correctly(): void
    {
        $type = new Types\ObjectType();

        self::assertTrue($type->matches(new stdClass()));
        self::assertFalse($type->matches(1.0));
    }

    /**
     * @test
     */
    public function string_types_matches_correctly(): void
    {
        $type = new Types\StringType();

        self::assertTrue($type->matches('1'));
        self::assertFalse($type->matches(1));
    }

    /**
     * @test
     */
    public function union_types_matches_correctly(): void
    {
        $type = new Types\UnionType(new Types\StringType(), new Types\IntType());

        self::assertTrue($type->matches('1'));
        self::assertTrue($type->matches(1));
        self::assertFalse($type->matches(1.0));
    }

    /**
     * @test
     */
    public function void_types_matches_correctly(): void
    {
        $type = new Types\VoidType();

        self::assertTrue($type->matches(null));
        self::assertFalse($type->matches(1));
        self::assertFalse($type->matches(1.0));
        self::assertFalse($type->matches('string'));
        self::assertFalse($type->matches(false));
        self::assertFalse($type->matches(new Types\StringType()));
    }
}