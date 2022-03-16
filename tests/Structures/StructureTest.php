<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Structures;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Elements\Structure;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Support\Visibility;
use Smpl\Inspector\Tests\Fixtures\ExampleTrait;
use Smpl\Inspector\Tests\Fixtures\TypeReflectableClass;
use Smpl\Inspector\Types\ArrayType;
use Smpl\Inspector\Types\BaseType;
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
use Traversable;

/**
 * @group factories
 * @group structures
 */
class StructureTest extends TestCase
{
    private TypeFactory      $types;
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->types   = new TypeFactory();
        $this->factory = new StructureFactory($this->types);
    }

    /**
     * @test
     */
    public function accurately_represents_a_class(): void
    {
        $structure = $this->factory->makeStructure(Structure::class);

        self::assertSame('Structure', $structure->getName());
        self::assertSame(Structure::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Elements', $structure->getNamespace());
        self::assertSame(StructureType::Default, $structure->getStructureType());
        self::assertSame(Structure::class, $structure->getType()->getName());
        self::assertTrue($structure->isInstantiable());
        self::assertNull($structure->getParent());
    }

    /**
     * @test
     */
    public function accurately_represents_a_class_with_a_parent(): void
    {
        $string = $this->factory->makeStructure(StringType::class);
        $parent = $string->getParent();

        self::assertSame('StringType', $string->getName());
        self::assertSame(StringType::class, $string->getFullName());
        self::assertSame('Smpl\Inspector\Types', $string->getNamespace());
        self::assertSame(StructureType::Default, $string->getStructureType());
        self::assertSame(StringType::class, $string->getType()->getName());
        self::assertTrue($string->isInstantiable());

        self::assertNotNull($parent);
        self::assertSame('BaseType', $parent->getName());
        self::assertSame(BaseType::class, $parent->getFullName());
        self::assertSame('Smpl\Inspector\Types', $parent->getNamespace());
        self::assertSame(StructureType::Default, $parent->getStructureType());
        self::assertSame(BaseType::class, $parent->getType()->getName());
        self::assertFalse($parent->isInstantiable());
        self::assertNull($parent->getParent());
    }

    /**
     * @test
     */
    public function accurately_represents_an_interface(): void
    {
        $structure = $this->factory->makeStructure(StructureContract::class);

        self::assertSame('Structure', $structure->getName());
        self::assertSame(StructureContract::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Contracts', $structure->getNamespace());
        self::assertSame(StructureType::Interface, $structure->getStructureType());
        self::assertSame(StructureContract::class, $structure->getType()->getName());
        self::assertFalse($structure->isInstantiable());
        self::assertNull($structure->getParent());
    }

    /**
     * @test
     */
    public function accurately_represents_an_enum(): void
    {
        $structure = $this->factory->makeStructure(StructureType::class);

        self::assertSame('StructureType', $structure->getName());
        self::assertSame(StructureType::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Support', $structure->getNamespace());
        self::assertSame(StructureType::Enum, $structure->getStructureType());
        self::assertSame(StructureType::class, $structure->getType()->getName());
        self::assertFalse($structure->isInstantiable());
        self::assertNull($structure->getParent());
    }

    /**
     * @test
     */
    public function accurately_represents_a_trait(): void
    {
        $structure = $this->factory->makeStructure(ExampleTrait::class);

        self::assertSame('ExampleTrait', $structure->getName());
        self::assertSame(ExampleTrait::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Tests\Fixtures', $structure->getNamespace());
        self::assertSame(StructureType::Trait, $structure->getStructureType());
        self::assertSame(ExampleTrait::class, $structure->getType()->getName());
        self::assertFalse($structure->isInstantiable());
        self::assertNull($structure->getParent());
    }
}