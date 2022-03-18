<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Structures;

use Iterator;
use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Inspector;
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
 * @group attributes
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
    public function correctly_loads_attributes_for_classes(): void
    {
        $structure   = $this->factory->makeStructure(ClassWithAttributes::class);
        $structure2  = $this->factory->makeStructure(TypeReflectableClass::class);
        $attributes  = $structure->getAttributes();
        $attributes2 = $structure2->getAttributes();

        self::assertIsIterable($attributes);
        self::assertSame($structure, $attributes->getStructure());
        self::assertSame(AttributeTarget::Structure, $attributes->getAttributeTarget());
        self::assertCount(1, $attributes);
        self::assertTrue($attributes->has(TestAttribute::class));
        self::assertCount(2, $attributes->metadata(TestAttribute::class));

        self::assertCount(0, $attributes2);
    }

    /**
     * @test
     */
    public function correctly_reads_attribute_flags(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);
        $attribute = $structure->getAttributes()->get(TestAttribute::class);

        self::assertNotNull($attribute);
        self::assertTrue($attribute->isRepeatable());
        self::assertContains(AttributeTarget::Constant, $attribute->getTargets());
        self::assertContains(AttributeTarget::Parameter, $attribute->getTargets());
        self::assertContains(AttributeTarget::Property, $attribute->getTargets());
        self::assertContains(AttributeTarget::Structure, $attribute->getTargets());
        self::assertContains(AttributeTarget::Function, $attribute->getTargets());
        self::assertContains(AttributeTarget::Method, $attribute->getTargets());
    }

    /**
     * @test
     */
    public function correctly_create_metadata_instances(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);
        $attribute = $structure->getAttributes()->get(TestAttribute::class);
        $metadata  = $structure->getAttributes()->metadata(TestAttribute::class);

        self::assertNotNull($metadata);
        self::assertIsIterable($metadata);
        self::assertCount(2, $metadata);
        self::assertSame($attribute, $metadata->getAttribute());
        self::assertInstanceOf(TestAttribute::class, $metadata->getMetadata()[0]->getInstance());
        self::assertSame($attribute, $metadata->getMetadata()[0]->getAttribute());
        self::assertInstanceOf(TestAttribute::class, $metadata->getMetadata()[1]->getInstance());
        self::assertSame($attribute, $metadata->getMetadata()[1]->getAttribute());
    }

    /**
     * @test
     */
    public function correctly_identifies_attribute_structures(): void
    {
        $structure = $this->factory->makeStructure(TestAttribute::class);

        self::assertSame(StructureType::Attribute, $structure->getStructureType());
    }

    /**
     * @test
     */
    public function does_not_read_php_base_attribute_as_an_attribute(): void
    {
        $structure = $this->factory->makeStructure(TestAttribute::class);

        self::assertCount(0, $structure->getAttributes());
    }

    /**
     * @test
     */
    public function attribute_collections_are_iterable(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);

        self::assertInstanceOf(Traversable::class, $structure->getAttributes());
        self::assertInstanceOf(Iterator::class, $structure->getAttributes()->getIterator());
    }

    /**
     * @test
     */
    public function metadata_collections_are_iterable(): void
    {
        $structure = $this->factory->makeStructure(ClassWithAttributes::class);
        $metadata  = $structure->getAttributes()->metadata(TestAttribute::class);

        self::assertInstanceOf(Traversable::class, $metadata);
        self::assertInstanceOf(Iterator::class, $metadata->getIterator());
    }

    /**
     * @test
     */
    public function correctly_loads_attributes_for_methods(): void
    {
        $structure   = $this->factory->makeStructure(ClassWithAttributes::class);
        $method      = $structure->getMethods()->get('__construct');
        $method2     = $structure->getMethods()->get('noAttributeMethod');
        $attributes  = $method->getAttributes();
        $attributes2 = $method2->getAttributes();

        self::assertIsIterable($attributes);
        self::assertSame($method, $attributes->getMethod());
        self::assertSame(AttributeTarget::Method, $attributes->getAttributeTarget());
        self::assertCount(1, $attributes);
        self::assertFalse($attributes->has(TestAttribute::class));
        self::assertNull($attributes->metadata(TestAttribute::class));
        self::assertTrue($attributes->has(MethodAttribute::class));
        self::assertCount(1, $attributes->metadata(MethodAttribute::class));

        self::assertIsIterable($attributes2);
        self::assertSame($method2, $attributes2->getMethod());
        self::assertSame(AttributeTarget::Method, $attributes2->getAttributeTarget());
        self::assertCount(0, $attributes2);
    }

    /**
     * @test
     */
    public function correctly_loads_attributes_for_properties(): void
    {
        $structure   = $this->factory->makeStructure(ClassWithAttributes::class);
        $property    = $structure->getProperties()->get('property');
        $property2   = $structure->getProperties()->get('noAttributeProperty');
        $attributes  = $property->getAttributes();
        $attributes2 = $property2->getAttributes();

        self::assertIsIterable($attributes);
        self::assertSame($property, $attributes->getProperty());
        self::assertSame(AttributeTarget::Property, $attributes->getAttributeTarget());
        self::assertCount(1, $attributes);
        self::assertTrue($attributes->has(TestAttribute::class));
        self::assertCount(1, $attributes->metadata(TestAttribute::class));

        self::assertIsIterable($attributes2);
        self::assertSame($property2, $attributes2->getProperty());
        self::assertSame(AttributeTarget::Property, $attributes2->getAttributeTarget());
        self::assertCount(0, $attributes2);
    }

    /**
     * @test
     */
    public function correctly_loads_attributes_for_parameters(): void
    {
        $structure   = $this->factory->makeStructure(ClassWithAttributes::class);
        $parameters  = $structure->getMethods()->get('__construct')->getParameters();
        $parameter   = $parameters->get(0);
        $attributes  = $parameter->getAttributes();
        $parameter2  = $parameters->get(1);
        $attributes2 = $parameter2->getAttributes();


        self::assertIsIterable($attributes);
        self::assertSame($parameter, $attributes->getParameter());
        self::assertSame(AttributeTarget::Parameter, $attributes->getAttributeTarget());
        self::assertCount(1, $attributes);
        self::assertTrue($attributes->has(TestAttribute::class));
        self::assertCount(1, $attributes->metadata(TestAttribute::class));

        self::assertIsIterable($attributes2);
        self::assertSame($parameter2, $attributes2->getParameter());
        self::assertSame(AttributeTarget::Parameter, $attributes2->getAttributeTarget());
        self::assertCount(0, $attributes2);
    }
}