<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Types;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Tests\Fixtures\TypeReflectableClass;
use Smpl\Inspector\Types;

/**
 * @group factories
 * @group reflection
 * @group types
 */
class TypeFactoryFromReflectionTest extends TestCase
{
    private TypeFactory     $factory;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->factory    = new TypeFactory;
        $this->reflection = new ReflectionClass(TypeReflectableClass::class);
    }

    /**
     * @test
     */
    public function can_make_array_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('array')->getType());

        self::assertInstanceOf(Types\ArrayType::class, $type);
        self::assertSame('array', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_bool_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('bool')->getType());

        self::assertInstanceOf(Types\BoolType::class, $type);
        self::assertSame('bool', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_class_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('class')->getType());

        self::assertInstanceOf(Types\ClassType::class, $type);
        self::assertSame(Type::class, $type->getName());
    }

    /**
     * @test
     */
    public function can_make_float_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('float')->getType());

        self::assertInstanceOf(Types\FloatType::class, $type);
        self::assertSame('float', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_int_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('int')->getType());

        self::assertInstanceOf(Types\IntType::class, $type);
        self::assertSame('int', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_iterable_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('iterable')->getType());

        self::assertInstanceOf(Types\IterableType::class, $type);
        self::assertSame('iterable', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_mixed_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('mixed')->getType());

        self::assertInstanceOf(Types\MixedType::class, $type);
        self::assertSame('mixed', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_object_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('object')->getType());

        self::assertInstanceOf(Types\ObjectType::class, $type);
        self::assertSame('object', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_string_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('string')->getType());

        self::assertInstanceOf(Types\StringType::class, $type);
        self::assertSame('string', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_void_types(): void
    {
        $type = $this->factory->make($this->reflection->getMethod('voidReturn')->getReturnType());

        self::assertInstanceOf(Types\VoidType::class, $type);
        self::assertSame('void', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_nullable_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('nullable')->getType());

        self::assertInstanceOf(Types\NullableType::class, $type);
        self::assertSame('string|null', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_union_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('union')->getType());

        self::assertInstanceOf(Types\UnionType::class, $type);
        self::assertSame('int|string', $type->getName());
    }

    /**
     * @test
     */
    public function can_make_intersection_types(): void
    {
        $type = $this->factory->make($this->reflection->getProperty('intersection')->getType());

        self::assertInstanceOf(Types\IntersectionType::class, $type);
        self::assertSame(Type::class . '&' . Types\StringType::class, $type->getName());
    }
}