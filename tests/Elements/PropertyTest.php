<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Collections\Properties;
use Smpl\Inspector\Contracts\PropertyCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Smpl\Inspector\Elements\InheritedProperty;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Filters\PropertyFilter;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;
use Smpl\Inspector\Tests\Fixtures\ClassAttribute;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\IntersectionClass;
use Smpl\Inspector\Tests\Fixtures\PropertyAttribute;
use Smpl\Inspector\Types\FloatType;
use Smpl\Inspector\Types\IntType;
use Smpl\Inspector\Types\StringType;

/**
 * @group elements
 * @group properties
 */
class PropertyTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = Inspector::getInstance()->structures();
    }

    /**
     * @test
     */
    public function properties_know_their_declaring_structure(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertSame(
            $structure->getProperty('publicStringProperty')->getStructure(),
            $structure->getProperty('publicStringProperty')->getDeclaringStructure()
        );
        self::assertNotSame(
            $structure->getProperty('iAmAnInheritedProperty')->getStructure(),
            $structure->getProperty('iAmAnInheritedProperty')->getDeclaringStructure()
        );
    }

    /**
     * @test
     */
    public function properties_provide_their_short_name(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $property  = $structure->getProperty('publicStringProperty');

        self::assertSame('publicStringProperty', $property->getName());
    }

    /**
     * @test
     */
    public function properties_provide_their_full_name_including_class_name(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $property  = $structure->getProperty('publicStringProperty');
        $name      = ExampleClass::class . Structure::SEPARATOR . 'publicStringProperty';

        self::assertSame($name, $property->getFullName());
    }

    /**
     * @test
     */
    public function properties_have_the_correct_visibility(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertSame(
            Visibility::Public,
            $structure->getProperty('publicStringProperty')->getVisibility()
        );
        self::assertSame(
            Visibility::Protected,
            $structure->getProperty('protectedMixedProperty')->getVisibility()
        );
        self::assertSame(
            Visibility::Private,
            $structure->getProperty('nullablePrivateIntProperty')->getVisibility()
        );
    }

    /**
     * @test
     */
    public function properties_know_if_they_are_inherited(): void
    {
        $structure    = $this->factory->makeStructure(ExampleClass::class);
        $notInherited = $structure->getProperty('publicStringProperty');
        $inherited    = $structure->getProperty('iAmAnInheritedProperty');

        self::assertTrue($inherited->isInherited());
        self::assertInstanceOf(InheritedProperty::class, $inherited);
        self::assertFalse($notInherited->isInherited());
        self::assertNotInstanceOf(InheritedProperty::class, $notInherited);
    }

    /**
     * @test
     */
    public function properties_know_if_they_are_static(): void
    {
        $structure         = $this->factory->makeStructure(ExampleClass::class);
        $staticProperty    = $structure->getProperty('mixed');
        $nonStaticProperty = $structure->getProperty('publicStringProperty');

        self::assertTrue($staticProperty->isStatic());
        self::assertFalse($nonStaticProperty->isStatic());
    }

    /**
     * @test
     */
    public function properties_know_if_they_are_promoted(): void
    {
        $structure   = $this->factory->makeStructure(ExampleClass::class);
        $promoted    = $structure->getProperty('promotedPublicBoolProperty');
        $notPromoted = $structure->getProperty('publicStringProperty');

        self::assertTrue($promoted->isPromoted());
        self::assertFalse($notPromoted->isPromoted());
    }

    /**
     * @test
     */
    public function properties_know_if_they_are_nullable(): void
    {
        $structure   = $this->factory->makeStructure(ExampleClass::class);
        $nullable    = $structure->getProperty('nullablePrivateIntProperty');
        $notNullable = $structure->getProperty('publicStringProperty');

        self::assertTrue($nullable->isNullable());
        self::assertFalse($notNullable->isNullable());
    }

    /**
     * @test
     */
    public function properties_know_if_they_have_a_default_value(): void
    {
        $structure    = $this->factory->makeStructure(ExampleClass::class);
        $hasDefault   = $structure->getProperty('nullablePrivateIntPropertyWithDefault');
        $hasNoDefault = $structure->getProperty('protectedMixedProperty');

        self::assertTrue($hasDefault->hasDefault());
        self::assertFalse($hasNoDefault->hasDefault());
    }

    /**
     * @test
     */
    public function properties_know_their_default_value(): void
    {
        $structure    = $this->factory->makeStructure(ExampleClass::class);
        $hasDefault   = $structure->getProperty('nullablePrivateIntPropertyWithDefault');
        $hasNoDefault = $structure->getProperty('protectedMixedProperty');

        self::assertSame(3, $hasDefault->getDefault());
        self::assertNull($hasNoDefault->getDefault());
    }

    /**
     * @test
     */
    public function properties_know_their_type(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $noType    = $structure->getProperty('mixed');
        $hasType   = $structure->getProperty('publicStringProperty');

        self::assertNull($noType->getType());
        self::assertNotNull($hasType->getType());
        self::assertInstanceOf(StringType::class, $hasType->getType());
    }

    /**
     * @test
     */
    public function properties_have_a_collection_of_metadata(): void
    {
        $structure     = $this->factory->makeStructure(ExampleClass::class);
        $oneAttribute  = $structure->getProperty('publicStringProperty')->getAllMetadata();
        $notAttributes = $structure->getProperty('nullablePrivateIntPropertyWithDefault')->getAllMetadata();

        self::assertCount(1, $oneAttribute);
        self::assertCount(0, $notAttributes);
    }

    /**
     * @test
     */
    public function properties_can_check_if_they_have_a_single_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $property  = $structure->getProperty('publicStringProperty');

        self::assertTrue($property->hasAttribute(PropertyAttribute::class));
        self::assertFalse($property->hasAttribute(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function properties_can_retrieve_a_single_piece_of_metadata(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $property  = $structure->getProperty('publicStringProperty');

        self::assertNotEmpty($property->getMetadata(PropertyAttribute::class));
        self::assertEmpty($property->getMetadata(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function properties_can_retrieve_the_first_piece_of_metadata_for_an_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $property  = $structure->getProperty('publicStringProperty');

        self::assertNotNull($property->getFirstMetadata(PropertyAttribute::class));
    }

    /**
     * @test
     */
    public function properties_have_access_to_an_array_of_attributes(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $property   = $structure->getProperty('publicStringProperty');
        $attributes = $property->getAttributes();

        self::assertCount(1, $attributes);
        self::assertSame(PropertyAttribute::class, $attributes[0]->getName());
    }

    /**
     * @test
     */
    public function property_collections_are_iterable(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();
        $iterator   = $collection->getIterator();

        self::assertIsIterable($collection);
        self::assertIsIterable($iterator);
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_all_properties(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();
        $values     = $collection->values();

        self::assertIsArray($values);
        self::assertCount($collection->count(), $values);
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_all_properties_as_a_list(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();
        $values     = $collection->values();

        self::assertTrue(array_is_list($values));
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_the_first_property(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertSame('publicStringProperty', $collection->first()->getName());
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_properties_by_their_index(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();
        $property   = $collection->indexOf(0);

        self::assertSame('publicStringProperty', $property->getName());
    }

    /**
     * @test
     */
    public function property_collections_return_null_for_non_existent_property_names(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertNull($collection->get('iDontExist'));
    }

    /**
     * @test
     */
    public function property_collections_return_null_for_non_existent_property_index(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertNull($collection->indexOf(99));
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_properties_by_their_full_name_including_class(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();
        $property   = $collection->get(ExampleClass::class . Structure::SEPARATOR . 'publicStringProperty');

        self::assertSame('publicStringProperty', $property->getName());
    }

    /**
     * @test
     */
    public function property_collections_let_you_check_if_a_property_exists_by_its_full_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertTrue($collection->has(ExampleClass::class . Structure::SEPARATOR . 'publicStringProperty'));
    }

    /**
     * @test
     */
    public function property_collections_correctly_report_their_empty_state(): void
    {
        $structure1  = $this->factory->makeStructure(ExampleClass::class);
        $collection1 = $structure1->getProperties();

        $structure2  = $this->factory->makeStructure(IntersectionClass::class);
        $collection2 = $structure2->getProperties();

        self::assertFalse($collection1->isEmpty());
        self::assertTrue($collection1->isNotEmpty());

        self::assertTrue($collection2->isEmpty());
        self::assertFalse($collection2->isNotEmpty());
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_the_full_names_of_all_properties(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();
        $names      = $collection->names();

        self::assertCount($collection->count(), $names);

        foreach ($names as $name) {
            self::assertTrue(str_contains($name, Structure::SEPARATOR));
            self::assertTrue($collection->has($name));
        }
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_the_short_names_of_all_properties(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties()->asBase();
        $names      = $collection->names(false);

        self::assertCount($collection->count(), $names);

        foreach ($names as $name) {
            self::assertFalse(str_contains($name, Structure::SEPARATOR));
            self::assertFalse($collection->has($name));
        }
    }

    /**
     * @test
     */
    public function property_collections_let_you_retrieve_the_short_unique_names_of_all_properties(): void
    {
        $structure1  = $this->factory->makeStructure(ExampleClass::class);
        $collection1 = $structure1->getProperties()->asBase();

        $structure2  = $this->factory->makeStructure(ExampleClass::class);
        $collection2 = $structure2->getProperties()->asBase();

        $methods = new Properties(array_merge($collection1->values(), $collection2->values()));

        self::assertCount($collection1->count(), $methods->names(false));
    }

    /**
     * @test
     */
    public function structure_property_collections_let_you_retrieve_properties_by_their_short_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();
        $property   = $collection->get('publicStringProperty');

        self::assertSame('publicStringProperty', $property->getName());
    }

    /**
     * @test
     */
    public function structure_property_collections_let_you_check_if_a_property_exists_by_its_short_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertTrue($collection->has('publicStringProperty'));
    }

    /**
     * @test
     */
    public function structure_property_collections_can_be_converted_to_their_base_collection(): void
    {
        $structure      = $this->factory->makeStructure(ExampleClass::class);
        $collection     = $structure->getProperties();
        $baseCollection = $collection->asBase();

        self::assertInstanceOf(PropertyCollection::class, $collection);
        self::assertNotInstanceOf(StructurePropertyCollection::class, $baseCollection);
    }

    /**
     * @test
     */
    public function property_collections_can_be_filtered_by_their_visibility(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertCount(5, $collection->filter(PropertyFilter::make()->publicOnly()));
        self::assertCount(2, $collection->filter(PropertyFilter::make()->protectedOnly()));
        self::assertCount(2, $collection->filter(PropertyFilter::make()->privateOnly()));
        self::assertCount(7, $collection->filter(PropertyFilter::make()->hasVisibility(
            Visibility::Public, Visibility::Protected
        )));
    }

    /**
     * @test
     */
    public function property_collections_can_be_filtered_by_whether_they_have_a_type(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertCount(1, $collection->filter(PropertyFilter::make()->notTyped()));
        self::assertCount(8, $collection->filter(PropertyFilter::make()->typed()));
    }

    /**
     * @test
     */
    public function property_collections_can_be_filtered_by_their_return_type(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertCount(2, $collection->filter(
            PropertyFilter::make()->hasType('string')
        ));
        self::assertCount(2, $collection->filter(
            PropertyFilter::make()->hasType(new StringType())
        ));
        self::assertCount(2, $collection->filter(
            PropertyFilter::make()->hasType('int')
        ));
        self::assertCount(2, $collection->filter(
            PropertyFilter::make()->hasType(new IntType())
        ));
        self::assertCount(0, $collection->filter(
            PropertyFilter::make()->hasType(new FloatType())
        ));
    }

    /**
     * @test
     */
    public function property_collections_can_be_filtered_by_whether_they_are_static(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertCount(1, $collection->filter(PropertyFilter::make()->static()));
        self::assertCount(8, $collection->filter(PropertyFilter::make()->notStatic()));
    }

    /**
     * @test
     */
    public function property_collections_can_be_filtered_by_whether_they_are_nullable(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertCount(4, $collection->filter(PropertyFilter::make()->nullable()));
        self::assertCount(5, $collection->filter(PropertyFilter::make()->notNullable()));
    }

    /**
     * @test
     */
    public function property_collections_can_be_filtered_by_whether_they_have_a_default_value(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertCount(2, $collection->filter(PropertyFilter::make()->hasDefaultValue()));
        self::assertCount(7, $collection->filter(PropertyFilter::make()->noDefaultValue()));
    }

    /**
     * @test
     */
    public function property_collections_can_be_filtered_by_whether_they_have_a_specific_attribute(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getProperties();

        self::assertCount(2, $collection->filter(
            PropertyFilter::make()->hasAttribute(PropertyAttribute::class))
        );
    }
}