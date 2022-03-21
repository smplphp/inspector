<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Structures;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Elements\Structure;
use Smpl\Inspector\Exceptions\StructureException;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Tests\Fixtures\ExampleTrait;

/**
 * @group factories
 * @group structures
 */
class StructureFactoryTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StructureFactory(new TypeFactory());
    }

    /**
     * @test
     */
    public function can_create_structure_for_class_name(): void
    {
        $structure = $this->factory->makeStructure(Structure::class);

        self::assertSame('Structure', $structure->getName());
        self::assertSame(Structure::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Elements', $structure->getNamespace());
        self::assertSame(StructureType::Default, $structure->getStructureType());
        self::assertSame(Structure::class, $structure->getType()->getName());
    }

    /**
     * @test
     */
    public function can_create_structure_for_interface_name(): void
    {
        $structure = $this->factory->makeStructure(StructureContract::class);

        self::assertSame('Structure', $structure->getName());
        self::assertSame(StructureContract::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Contracts', $structure->getNamespace());
        self::assertSame(StructureType::Interface, $structure->getStructureType());
        self::assertSame(StructureContract::class, $structure->getType()->getName());
    }

    /**
     * @test
     */
    public function can_create_structure_for_enum_name(): void
    {
        $structure = $this->factory->makeStructure(StructureType::class);

        self::assertSame('StructureType', $structure->getName());
        self::assertSame(StructureType::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Support', $structure->getNamespace());
        self::assertSame(StructureType::Enum, $structure->getStructureType());
        self::assertSame(StructureType::class, $structure->getType()->getName());
    }

    /**
     * @test
     */
    public function can_create_structure_for_trait_name(): void
    {
        $structure = $this->factory->makeStructure(ExampleTrait::class);

        self::assertSame('ExampleTrait', $structure->getName());
        self::assertSame(ExampleTrait::class, $structure->getFullName());
        self::assertSame('Smpl\Inspector\Tests\Fixtures', $structure->getNamespace());
        self::assertSame(StructureType::Trait, $structure->getStructureType());
        self::assertSame(ExampleTrait::class, $structure->getType()->getName());
    }

    /**
     * @test
     */
    public function throws_an_exception_when_the_class_is_invalid(): void
    {
        $this->expectException(StructureException::class);
        $this->expectExceptionMessage('Provided class \'invalid\' is not valid');

        $this->factory->makeStructure('invalid');
    }

    /**
     * @test
     */
    public function only_makes_one_instance_per_class(): void
    {
        $structure1 = $this->factory->makeStructure(Structure::class);
        $structure2 = $this->factory->makeStructure(Structure::class);

        self::assertSame($structure1, $structure2);
    }
}