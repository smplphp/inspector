<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\AttributeTarget;
use Smpl\Inspector\Tests\Fixtures\AttributeInterface;
use Smpl\Inspector\Tests\Fixtures\ClassAttribute;
use Smpl\Inspector\Tests\Fixtures\EmptyishClass;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\MethodAttribute;
use Smpl\Inspector\Tests\Fixtures\ParameterAttribute;
use Smpl\Inspector\Tests\Fixtures\PropertyAttribute;
use Smpl\Inspector\Tests\Fixtures\SecondClassAttribute;

/**
 * @group elements
 * @group metadata
 */
class MetadataTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = StructureFactory::getInstance();
    }

    /**
     * @test
     */
    public function metadata_contains_its_parent_attribute(): void
    {
        $collection = $this->factory->makeStructure(ExampleClass::class)->getAllMetadata();
        $metadata   = $collection->first();

        self::assertSame($metadata->getAttribute(), $this->factory->makeAttribute(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function metadata_will_create_an_instance_of_the_underlying_metadata(): void
    {
        $collection = $this->factory->makeStructure(ExampleClass::class)->getAllMetadata();
        $metadata   = $collection->first()->getInstance();

        self::assertInstanceOf(ClassAttribute::class, $metadata);
    }

    /**
     * @test
     */
    public function metadata_will_only_create_one_instance_of_the_underlying_metadata(): void
    {
        $collection = $this->factory->makeStructureMetadata($this->factory->makeStructure(ExampleClass::class));
        $metadata1  = $collection->first()->getInstance();
        $metadata2  = $collection->first()->getInstance();

        self::assertSame($metadata1, $metadata2);
    }

    /**
     * @test
     */
    public function metadata_collections_are_iterable(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $iterator   = $collection->getIterator();

        self::assertIsIterable($collection);
        self::assertIsIterable($iterator);
    }

    /**
     * @test
     */
    public function metadata_collections_let_you_retrieve_all_metadata_for_an_attribute_class(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $metadata   = $collection->get(ClassAttribute::class);

        self::assertCount(2, $metadata);
        self::assertEquals(ClassAttribute::class, $metadata[0]->getAttribute()->getName());
    }

    /**
     * @test
     */
    public function metadata_collections_return_empty_arrays_for_non_existent_metadata(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $metadata   = $collection->get(AttributeInterface::class);

        self::assertIsArray($metadata);
        self::assertCount(0, $metadata);
    }

    /**
     * @test
     */
    public function metadata_collections_let_you_check_if_they_have_metadata_for_an_attribute_class(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();

        self::assertTrue($collection->has(ClassAttribute::class));
        self::assertFalse($collection->has(AttributeInterface::class));
    }

    /**
     * @test
     */
    public function metadata_collections_let_you_check_if_they_have_metadata_for_an_attribute_class_with_an_instanceof_check(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();

        self::assertTrue($collection->has(ClassAttribute::class, true));
        self::assertTrue($collection->has(AttributeInterface::class, true));
    }

    /**
     * @test
     */
    public function metadata_collections_let_you_retrieve_the_first_piece_of_metadata_by_its_attribute_class(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $metadata   = $collection->first(ClassAttribute::class);

        self::assertNotNull($metadata);
        self::assertEquals(ClassAttribute::class, $metadata->getAttribute()->getName());
    }

    /**
     * @test
     */
    public function metadata_collections_can_count_metadata_by_their_attribute_class(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();

        self::assertEquals(2, $collection->countInstances(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function metadata_collections_let_you_retrieve_all_metadata_for_an_attribute_class_with_an_instanceof_check(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $metadata   = $collection->get(AttributeInterface::class, true);

        self::assertCount(3, $metadata);
        self::assertEquals(ClassAttribute::class, $metadata[0]->getAttribute()->getName());
    }

    /**
     * @test
     */
    public function metadata_collections_return_empty_arrays_for_metadata_attribute_class_with_an_instanceof_check_for_attributes_they_dont_have(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $metadata   = $collection->get(AttributeInterface::class, false);

        self::assertEmpty($metadata);
        self::assertCount(0, $metadata);
    }

    /**
     * @test
     */
    public function metadata_collections_let_you_retrieve_the_first_piece_of_metadata_by_its_attribute_class_with_an_instanceof_check(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $metadata   = $collection->first(AttributeInterface::class, true);

        self::assertNotNull($metadata);
        self::assertEquals(ClassAttribute::class, $metadata->getAttribute()->getName());
    }

    /**
     * @test
     */
    public function metadata_collections_return_null_for_the_first_piece_of_metadata_by_its_attribute_class_if_it_isnt_present(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();

        self::assertNull($collection->first(MethodAttribute::class, true));
    }

    /**
     * @test
     */
    public function metadata_collections_can_count_metadata_by_their_attribute_class_with_an_instanceof_check(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();

        self::assertEquals(3, $collection->countInstances(AttributeInterface::class, true));
    }

    /**
     * @test
     */
    public function metadata_collections_correctly_report_their_empty_state(): void
    {
        $structure1  = $this->factory->makeStructure(ExampleClass::class);
        $collection1 = $structure1->getAllMetadata();
        $structure2  = $this->factory->makeStructure(EmptyishClass::class);
        $collection2 = $structure2->getAllMetadata();

        self::assertFalse($collection1->isEmpty());
        self::assertTrue($collection1->isNotEmpty());

        self::assertTrue($collection2->isEmpty());
        self::assertFalse($collection2->isNotEmpty());
    }

    /**
     * @test
     */
    public function metadata_collections_let_you_retrieve_an_attribute_by_its_class(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $attribute  = $collection->getAttribute(ClassAttribute::class);

        self::assertNotNull($attribute);
        self::assertEquals(ClassAttribute::class, $attribute->getName());
    }

    /**
     * @test
     */
    public function metadata_collections_return_null_for_attribtues_that_arent_present(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $attribute  = $collection->getAttribute(MethodAttribute::class);

        self::assertNull($collection->getAttribute(MethodAttribute::class));
    }

    /**
     * @test
     */
    public function metadata_collections_contain_all_metadata_attributes(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $attributes = $collection->getAttributes();

        self::assertCount(2, $attributes);
        self::assertArrayHasKey(0, $attributes);
        self::assertArrayHasKey(1, $attributes);
        self::assertEquals(ClassAttribute::class, $collection->getAttributes()[0]->getName());
        self::assertEquals(SecondClassAttribute::class, $collection->getAttributes()[1]->getName());
    }

    /**
     * @test
     */
    public function method_metadata_collections_know_their_attribute_target(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $method     = $structure->getMethod('attributedPublicMethodWithoutParameters');
        $collection = $method->getAllMetadata();
        $metadata   = $collection->first()->getInstance();

        self::assertInstanceOf(MethodAttribute::class, $metadata);
        self::assertSame(AttributeTarget::Method, $collection->getAttributeTarget());
        self::assertSame($method, $collection->getMethod());
    }

    /**
     * @test
     */
    public function parameter_metadata_collections_know_their_attribute_target(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $method     = $structure->getMethod('__construct');
        $parameter  = $method->getParameter('someNumber');
        $collection = $parameter->getAllMetadata();
        $metadata   = $collection->first()->getInstance();

        self::assertInstanceOf(ParameterAttribute::class, $metadata);
        self::assertSame(AttributeTarget::Parameter, $collection->getAttributeTarget());
        self::assertSame($parameter, $collection->getParameter());
    }

    /**
     * @test
     */
    public function property_metadata_collections_know_their_attribute_target(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $property   = $structure->getProperty('publicStringProperty');
        $collection = $property->getAllMetadata();
        $metadata   = $collection->first()->getInstance();

        self::assertInstanceOf(PropertyAttribute::class, $metadata);
        self::assertSame(AttributeTarget::Property, $collection->getAttributeTarget());
        self::assertSame($property, $collection->getProperty());
    }

    /**
     * @test
     */
    public function structure_metadata_collections_know_their_attribute_target(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getAllMetadata();
        $metadata   = $collection->first()->getInstance();

        self::assertInstanceOf(ClassAttribute::class, $metadata);
        self::assertSame(AttributeTarget::Structure, $collection->getAttributeTarget());
        self::assertSame($structure, $collection->getStructure());
    }
}