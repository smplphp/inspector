<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Types;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Types;

/**
 * @group types
 */
class TypeBuiltInTest extends TestCase
{
    protected array $builtIn = [
        Types\ArrayType::class,
        Types\BoolType::class,
        Types\FloatType::class,
        Types\IntType::class,
        Types\IterableType::class,
        Types\MixedType::class,
        Types\ObjectType::class,
        Types\StringType::class,
        Types\VoidType::class,
    ];

    protected array $notBuiltIn = [
        Types\IntersectionType::class,
    ];

    /**
     * @test
     */
    public function built_in_types_report_built_in_status_correctly(): void
    {
        foreach ($this->builtIn as $class) {
            $type = new $class;
            self::assertTrue($type->isBuiltin());
        }
    }

    /**
     * @test
     */
    public function non_built_in_types_report_built_in_status_correctly(): void
    {
        foreach ($this->notBuiltIn as $class) {
            $type = new $class;
            self::assertFalse($type->isBuiltin());
        }
    }

    /**
     * @test
     */
    public function class_type_is_not_built_in(): void
    {
        self::assertFalse((new Types\ClassType(Types\StringType::class))->isBuiltin());
    }

    /**
     * @test
     */
    public function nullable_type_inherits_builtin_status(): void
    {
        self::assertTrue((new Types\NullableType(new Types\StringType()))->isBuiltin());
        self::assertFalse((new Types\NullableType(new Types\ClassType(Types\StringType::class)))->isBuiltin());
    }

    /**
     * @test
     */
    public function union_type_inherits_builtin_status(): void
    {
        self::assertTrue((new Types\UnionType(
            new Types\StringType(),
            new Types\IntType()
        ))->isBuiltin());

        self::assertFalse((new Types\UnionType(
            new Types\ClassType(Types\StringType::class),
            new Types\ClassType(Types\IntType::class)
        ))->isBuiltin());
    }
}