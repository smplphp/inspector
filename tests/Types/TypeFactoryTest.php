<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Types;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\TypeFactory;
use Smpl\Inspector\Types;
use Stringable;

/**
 * @group factories
 * @group types
 */
class TypeFactoryTest extends TestCase
{
    private TypeFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new TypeFactory;
    }

    /**
     * @test
     */
    public function can_make_array_types(): void
    {
        $type = $this->factory->make('array');

        self::assertInstanceOf(Types\ArrayType::class, $type);
        self::assertSame('array', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_bool_types(): void
    {
        $type = $this->factory->make('bool');

        self::assertInstanceOf(Types\BoolType::class, $type);
        self::assertSame('bool', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_class_types(): void
    {
        $type = $this->factory->make(TypeFactory::class);

        self::assertInstanceOf(Types\ClassType::class, $type);
        self::assertSame(TypeFactory::class, $type->getName());
    }

    /**
     * @test
     */
    public function can_make_float_types(): void
    {
        $type = $this->factory->make('float');

        self::assertInstanceOf(Types\FloatType::class, $type);
        self::assertSame('float', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_int_types(): void
    {
        $type = $this->factory->make('int');

        self::assertInstanceOf(Types\IntType::class, $type);
        self::assertSame('int', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_iterable_types(): void
    {
        $type = $this->factory->make('iterable');

        self::assertInstanceOf(Types\IterableType::class, $type);
        self::assertSame('iterable', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_mixed_types(): void
    {
        $type = $this->factory->make('mixed');

        self::assertInstanceOf(Types\MixedType::class, $type);
        self::assertSame('mixed', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_object_types(): void
    {
        $type = $this->factory->make('object');

        self::assertInstanceOf(Types\ObjectType::class, $type);
        self::assertSame('object', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_string_types(): void
    {
        $type = $this->factory->make('string');

        self::assertInstanceOf(Types\StringType::class, $type);
        self::assertSame('string', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_void_types(): void
    {
        $type = $this->factory->make('void');

        self::assertInstanceOf(Types\VoidType::class, $type);
        self::assertSame('void', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_nullable_types(): void
    {
        $type = $this->factory->makeNullable('string');

        self::assertInstanceOf(Types\NullableType::class, $type);
        self::assertSame('string|null', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_nullable_types_from_string(): void
    {
        $type = $this->factory->make('?string');

        self::assertInstanceOf(Types\NullableType::class, $type);
        self::assertSame('string|null', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_union_types(): void
    {
        $type = $this->factory->makeUnion(['string', Stringable::class]);

        self::assertInstanceOf(Types\UnionType::class, $type);
        self::assertSame(Stringable::class . '|string', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_union_types_from_string(): void
    {
        $unionString = Stringable::class . '|string';
        $type        = $this->factory->make($unionString);

        self::assertInstanceOf(Types\UnionType::class, $type);
        self::assertSame($unionString, $type->getName());
    }

    /**
     * @test
     */
    public function can_make_intersection_types(): void
    {
        $type = $this->factory->makeIntersection([Types\ArrayType::class, Types\BoolType::class]);

        self::assertInstanceOf(Types\IntersectionType::class, $type);
        self::assertSame(Types\ArrayType::class . '&' . Types\BoolType::class, $type->getName());
    }

    /**
     * @test
     */
    public function can_make_intersection_types_from_string(): void
    {
        $intersectionString = Types\ArrayType::class . '&' . Types\BoolType::class;
        $type               = $this->factory->make($intersectionString);

        self::assertInstanceOf(Types\IntersectionType::class, $type);
        self::assertSame($intersectionString, $type->getName());
    }

    /**
     * @test
     */
    public function ignores_pass_by_reference_types(): void
    {
        $type = $this->factory->make('&string');

        self::assertInstanceOf(Types\StringType::class, $type);
        self::assertSame('string', $type->getName());
    }

    /**
     * @test
     */
    public function only_makes_one_instance_of_base_types(): void
    {
        self::assertSame($this->factory->make('string'), $this->factory->make('string'));
    }

    /**
     * @test
     */
    public function only_makes_one_instance_of_union_types(): void
    {
        $unionString = Stringable::class . '|string';
        self::assertSame($this->factory->make($unionString), $this->factory->make($unionString));
    }

    /**
     * @test
     */
    public function only_makes_one_instance_of_intersection_types(): void
    {
        $intersectionString = Types\ArrayType::class . '&' . Types\BoolType::class;
        self::assertSame($this->factory->make($intersectionString), $this->factory->make($intersectionString));
    }

    /**
     * @test
     */
    public function does_not_nest_nullable_types(): void
    {
        $baseType = $this->factory->makeNullable('string');
        $type     = $this->factory->makeNullable($baseType);

        self::assertInstanceOf(Types\NullableType::class, $type);
        self::assertSame('string|null', $type->getName());
        self::assertSame($baseType, $type);
    }
}