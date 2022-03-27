<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Types;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Tests\Fixtures\BasicInterface;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\IntersectionClass;
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
use stdClass;

/**
 * @group types
 * @group intersection
 */
class IntersectionTypeTest extends TestCase
{
    private IntersectionType $type;

    protected function setUp(): void
    {
        $this->type = new IntersectionType(
            new ClassType(SecondInterface::class),
            new ClassType(BasicInterface::class)
        );
    }

    /**
     * @test
     */
    public function intersection_types_are_not_primitive(): void
    {
        self::assertFalse($this->type->isPrimitive());
    }

    /**
     * @test
     */
    public function intersection_types_are_not_builtin(): void
    {
        self::assertFalse($this->type->isBuiltin());
    }

    /**
     * @test
     */
    public function union_types_are_ordered_by_their_name(): void
    {
        $subtypes = $this->type->getSubtypes();

        self::assertSame(BasicInterface::class, $subtypes[0]->getName());
        self::assertSame(SecondInterface::class, $subtypes[1]->getName());
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_arrays(): void
    {
        self::assertFalse($this->type->matches([]));
    }

    /**
     * @test
     */
    public function intersection_dont_types_match_bools(): void
    {
        self::assertFalse($this->type->matches(false));
        self::assertFalse($this->type->matches(true));
    }

    /**
     * @test
     */
    public function intersection_types_matches_instances_of_both_of_its_classes(): void
    {
        self::assertTrue($this->type->matches(new IntersectionClass));
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_instances_of_one_of_its_classes(): void
    {
        self::assertFalse($this->type->matches(new ExampleClass(
            'test', 2, false
        )));
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_floats(): void
    {
        self::assertFalse($this->type->matches(3.5));
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_ints(): void
    {
        self::assertFalse($this->type->matches(0));
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_iterables(): void
    {
        self::assertFalse($this->type->matches(new ArrayIterator()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_null(): void
    {
        self::assertFalse($this->type->matches(null));
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_objects(): void
    {
        self::assertFalse($this->type->matches(new stdClass()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_match_strings(): void
    {
        self::assertFalse($this->type->matches('class'));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_array_types(): void
    {
        self::assertFalse($this->type->accepts('array'));
        self::assertFalse($this->type->accepts(new ArrayType()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_bool_types(): void
    {
        self::assertFalse($this->type->accepts('bool'));
        self::assertFalse($this->type->accepts(new BoolType()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_class_types_that_dont_match_both_their_subtypes(): void
    {
        self::assertFalse($this->type->accepts(BasicInterface::class));
        self::assertFalse($this->type->accepts(new ClassType(BasicInterface::class)));
    }

    /**
     * @test
     */
    public function intersection_types_accept_class_types_that_match_both_their_subtypes(): void
    {
        self::assertTrue($this->type->accepts(IntersectionClass::class));
        self::assertTrue($this->type->accepts(new ClassType(IntersectionClass::class)));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_float_types(): void
    {
        self::assertFalse($this->type->accepts('float'));
        self::assertFalse($this->type->accepts(new FloatType()));
    }

    /**
     * @test
     */
    public function intersection_types_accept_intersection_types_with_the_same_base_types(): void
    {
        self::assertTrue($this->type->accepts(
            BasicInterface::class . Type::INTERSECTION_SEPARATOR . SecondInterface::class
        ));
        self::assertTrue($this->type->accepts(new IntersectionType(
            new ClassType(BasicInterface::class),
            new ClassType(SecondInterface::class)
        )));
        self::assertTrue($this->type->accepts(
            SecondInterface::class . Type::INTERSECTION_SEPARATOR . BasicInterface::class
        ));
        self::assertTrue($this->type->accepts(new IntersectionType(
            new ClassType(SecondInterface::class),
            new ClassType(BasicInterface::class)
        )));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_int_types(): void
    {
        self::assertFalse($this->type->accepts('int'));
        self::assertFalse($this->type->accepts(new IntType()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_iterable_types(): void
    {
        self::assertFalse($this->type->accepts('iterable'));
        self::assertFalse($this->type->accepts(new IterableType()));
    }

    /**
     * @test
     */
    public function intersection_types_accept_mixed_types(): void
    {
        self::assertFalse($this->type->accepts('mixed'));
        self::assertFalse($this->type->accepts(new MixedType()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_nullable_types_with_different_base_types(): void
    {
        self::assertFalse($this->type->accepts('?array'));
        self::assertFalse($this->type->accepts(new NullableType(new ArrayType())));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_object_types(): void
    {
        self::assertFalse($this->type->accepts('object'));
        self::assertFalse($this->type->accepts(new ObjectType()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_string_types(): void
    {
        self::assertFalse($this->type->accepts('string'));
        self::assertFalse($this->type->accepts(new StringType()));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_union_types_with_different_base_types(): void
    {
        self::assertFalse($this->type->accepts('string|float'));
        self::assertFalse($this->type->accepts(new UnionType(
            new StringType(), new FloatType()
        )));
    }

    /**
     * @test
     */
    public function intersection_types_dont_accept_void_types(): void
    {
        self::assertFalse($this->type->accepts('void'));
        self::assertFalse($this->type->accepts(new VoidType()));
    }
}