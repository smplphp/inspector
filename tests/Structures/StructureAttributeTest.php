<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Structures;

use Iterator;
use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Support\AttributeTarget;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Tests\Fixtures\ClassWithAttributes;
use Smpl\Inspector\Tests\Fixtures\MethodAttribute;
use Smpl\Inspector\Tests\Fixtures\TestAttribute;
use Smpl\Inspector\Tests\Fixtures\TypeReflectableClass;
use Traversable;

/**
 * @group factories
 * @group structures
 * @group metadata
 */
class StructureAttributeTest extends TestCase
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
    public function correctly_loads_metadata_for_classes(): void
    {
        $structure  = $this->factory->makeStructure(ClassWithAttributes::class);
        $structure2 = $this->factory->makeStructure(TypeReflectableClass::class);
        $metadata   = $structure->getAllMetadata();
        $metadata2  = $structure2->getAllMetadata();

        self::assertIsIterable($metadata);
        self::assertSame($structure, $metadata->getStructure());
        self::assertSame(AttributeTarget::Structure, $metadata->getAttributeTarget());
        self::assertCount(2, $metadata);
        self::assertTrue($metadata->has(TestAttribute::class));
        self::assertCount(1, $structure->getAttributes());

        self::assertCount(0, $metadata2);
    }

    /**
     * @test
     */
    public function correctly_reads_metadatum_flags(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);
        $metadatum = $structure->getAllMetadata()->getAttribute(TestAttribute::class);

        self::assertNotNull($metadatum);
        self::assertTrue($metadatum->isRepeatable());
        self::assertContains(AttributeTarget::Constant, $metadatum->getTargets());
        self::assertContains(AttributeTarget::Parameter, $metadatum->getTargets());
        self::assertContains(AttributeTarget::Property, $metadatum->getTargets());
        self::assertContains(AttributeTarget::Structure, $metadatum->getTargets());
        self::assertContains(AttributeTarget::Function, $metadatum->getTargets());
        self::assertContains(AttributeTarget::Method, $metadatum->getTargets());
    }

    /**
     * @test
     */
    public function correctly_create_metadata_instances(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);
        $metadatum = $structure->getAllMetadata()->getAttribute(TestAttribute::class);
        $metadata  = $structure->getAllMetadata()->get(TestAttribute::class);

        self::assertNotNull($metadata);
        self::assertIsIterable($metadata);
        self::assertCount(2, $metadata);
        self::assertSame($metadatum, $metadata[0]->getAttribute());
        self::assertInstanceOf(TestAttribute::class, $metadata[0]->getInstance());
        self::assertSame($metadatum, $metadata[0]->getAttribute());
        self::assertInstanceOf(TestAttribute::class, $metadata[1]->getInstance());
        self::assertSame($metadatum, $metadata[1]->getAttribute());
    }

    /**
     * @test
     */
    public function correctly_identifies_metadatum_structures(): void
    {
        $structure = $this->factory->makeStructure(TestAttribute::class);

        self::assertSame(StructureType::Attribute, $structure->getStructureType());
    }

    /**
     * @test
     */
    public function does_not_read_php_base_metadatum_as_a_metadatum(): void
    {
        $structure = $this->factory->makeStructure(TestAttribute::class);

        self::assertCount(0, $structure->getAttributes());
    }

    /**
     * @test
     */
    public function metadata_collections_are_iterable(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);

        self::assertInstanceOf(Traversable::class, $structure->getAllMetadata());
        self::assertInstanceOf(Iterator::class, $structure->getAllMetadata()->getIterator());
    }

    /**
     * @test
     */
    public function correctly_loads_metadata_for_methods(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);
        $method    = $structure->getMethods()->get('__construct');
        $method2   = $structure->getMethods()->get('noAttributeMethod');
        $metadata  = $method->getAllMetadata();
        $metadata2 = $method2->getAllMetadata();

        self::assertIsIterable($metadata);
        self::assertSame($method, $metadata->getMethod());
        self::assertSame(AttributeTarget::Method, $metadata->getAttributeTarget());
        self::assertCount(1, $metadata);
        self::assertFalse($metadata->has(TestAttribute::class));
        self::assertEmpty($metadata->get(TestAttribute::class));
        self::assertTrue($metadata->has(MethodAttribute::class));
        self::assertCount(1, $metadata->get(MethodAttribute::class));

        self::assertIsIterable($metadata2);
        self::assertSame($method2, $metadata2->getMethod());
        self::assertSame(AttributeTarget::Method, $metadata2->getAttributeTarget());
        self::assertCount(0, $metadata2);
    }

    /**
     * @test
     */
    public function correctly_loads_metadata_for_properties(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);
        $property  = $structure->getProperties()->get('property');
        $property2 = $structure->getProperties()->get('noAttributeProperty');
        $metadata  = $property->getAllMetadata();
        $metadata2 = $property2->getAllMetadata();

        self::assertIsIterable($metadata);
        self::assertSame($property, $metadata->getProperty());
        self::assertSame(AttributeTarget::Property, $metadata->getAttributeTarget());
        self::assertCount(1, $metadata);
        self::assertTrue($metadata->has(TestAttribute::class));
        self::assertCount(1, $metadata->get(TestAttribute::class));

        self::assertIsIterable($metadata2);
        self::assertSame($property2, $metadata2->getProperty());
        self::assertSame(AttributeTarget::Property, $metadata2->getAttributeTarget());
        self::assertCount(0, $metadata2);
    }

    /**
     * @test
     */
    public function correctly_loads_metadata_for_parameters(): void
    {
        $structure  = $this->factory->makeStructure(ClassWithAttributes::class);
        $parameters = $structure->getMethods()->get('__construct')->getParameters();
        $parameter  = $parameters->get(0);
        $metadata   = $parameter->getAllMetadata();
        $parameter2 = $parameters->get(1);
        $metadata2  = $parameter2->getAllMetadata();


        self::assertIsIterable($metadata);
        self::assertSame($parameter, $metadata->getParameter());
        self::assertSame(AttributeTarget::Parameter, $metadata->getAttributeTarget());
        self::assertCount(1, $metadata);
        self::assertTrue($metadata->has(TestAttribute::class));
        self::assertCount(1, $metadata->get(TestAttribute::class));

        self::assertIsIterable($metadata2);
        self::assertSame($parameter2, $metadata2->getParameter());
        self::assertSame(AttributeTarget::Parameter, $metadata2->getAttributeTarget());
        self::assertCount(0, $metadata2);
    }
}