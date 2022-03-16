<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Structures;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Elements\Structure;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Tests\Fixtures\ExampleTrait;

/**
 * @group factories
 * @group reflection
 * @group structures
 */
class StructureFactoryFromReflectionTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StructureFactory(new TypeFactory());
    }

    /**
     * @test
     */
    public function can_create_structure_from_class_reflection(): void
    {
        $structure = $this->factory->makeStructure(new ReflectionClass(Structure::class));

        self::assertSame('Structure', $structure->getName());
        self::assertSame(Structure::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Elements', $structure->getNamespace());
        self::assertSame(StructureType::Default, $structure->getStructureType());
        self::assertSame(Structure::class, $structure->getType()->getName());
    }

    /**
     * @test
     */
    public function can_create_structure_from_interface_reflection(): void
    {
        $structure = $this->factory->makeStructure(new ReflectionClass(StructureContract::class));

        self::assertSame('Structure', $structure->getName());
        self::assertSame(StructureContract::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Contracts', $structure->getNamespace());
        self::assertSame(StructureType::Interface, $structure->getStructureType());
        self::assertSame(StructureContract::class, $structure->getType()->getName());
    }

    /**
     * @test
     */
    public function can_create_structure_from_enum_reflection(): void
    {
        $structure = $this->factory->makeStructure(new ReflectionClass(StructureType::class));

        self::assertSame('StructureType', $structure->getName());
        self::assertSame(StructureType::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Support', $structure->getNamespace());
        self::assertSame(StructureType::Enum, $structure->getStructureType());
        self::assertSame(StructureType::class, $structure->getType()->getName());
    }

    /**
     * @test
     */
    public function can_create_structure_from_trait_reflection(): void
    {
        $structure = $this->factory->makeStructure(new ReflectionClass(ExampleTrait::class));

        self::assertSame('ExampleTrait', $structure->getName());
        self::assertSame(ExampleTrait::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Tests\Fixtures', $structure->getNamespace());
        self::assertSame(StructureType::Trait, $structure->getStructureType());
        self::assertSame(ExampleTrait::class, $structure->getType()->getName());
    }
}