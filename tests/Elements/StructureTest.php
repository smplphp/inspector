<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Collections\Structures;
use Smpl\Inspector\Contracts\MetadataCollection;
use Smpl\Inspector\Contracts\MethodCollection;
use Smpl\Inspector\Contracts\PropertyCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Filters\MethodFilter;
use Smpl\Inspector\Filters\PropertyFilter;
use Smpl\Inspector\Filters\StructureFilter;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Tests\Fixtures\AttributeInterface;
use Smpl\Inspector\Tests\Fixtures\BasicInterface;
use Smpl\Inspector\Tests\Fixtures\BasicTrait;
use Smpl\Inspector\Tests\Fixtures\ClassAttribute;
use Smpl\Inspector\Tests\Fixtures\EmptyInterface;
use Smpl\Inspector\Tests\Fixtures\EmptyishClass;
use Smpl\Inspector\Tests\Fixtures\EmptyTrait;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\MethodAttribute;
use Smpl\Inspector\Tests\Fixtures\SecondExampleClass;
use Smpl\Inspector\Tests\Fixtures\SecondInterface;
use Smpl\Inspector\Types\ClassType;

/**
 * @group elements
 * @group structures
 */
class StructureTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = StructureFactory::getInstance();
    }

    /**
     * @test
     */
    public function structures_provide_their_class_name(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertSame('ExampleClass', $structure->getName());
    }

    /**
     * @test
     */
    public function structures_provide_their_namespace(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertSame('Smpl\Inspector\Tests\Fixtures', $structure->getNamespace());
    }

    /**
     * @test
     */
    public function structures_provide_their_full_name_including_namespace(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertStringContainsString($structure->getNamespace(), $structure->getFullName());
        self::assertStringContainsString($structure->getName(), $structure->getFullName());
        self::assertSame(ExampleClass::class, $structure->getFullName());
    }

    /**
     * @test
     */
    public function structures_provide_a_type_for_themselves(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $type      = $structure->getType();

        self::assertInstanceOf(ClassType::class, $type);
        self::assertSame($structure->getFullName(), $type->getName());
        self::assertTrue($type->accepts(ExampleClass::class));
    }

    /**
     * @test
     */
    public function structures_know_if_they_are_instantiable(): void
    {
        $structure1 = $this->factory->makeStructure(ExampleClass::class);
        $structure2 = $this->factory->makeStructure(BasicInterface::class);

        self::assertTrue($structure1->isInstantiable());
        self::assertFalse($structure2->isInstantiable());
    }

    /**
     * @test
     */
    public function structures_provide_their_parent_if_they_have_one(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $parent    = $structure->getParent();

        self::assertNotNull($parent);
        self::assertSame(EmptyishClass::class, $parent->getFullName());
    }

    /**
     * @test
     */
    public function structures_cache_their_parent_if_they_have_one(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $parent1   = $structure->getParent();
        $parent2   = $structure->getParent();

        self::assertNotNull($parent1);
        self::assertNotNull($parent2);
        self::assertSame($parent1, $parent2);
    }

    /**
     * @test
     */
    public function structures_provide_null_if_they_dont_have_a_parent(): void
    {
        $structure = $this->factory->makeStructure(BasicInterface::class);

        self::assertNull($structure->getParent());
    }

    /**
     * @test
     */
    public function structures_have_a_collection_of_properties(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $properties = $structure->getProperties();

        self::assertCount(9, $properties);
        self::assertInstanceOf(PropertyCollection::class, $properties);
    }

    /**
     * @test
     */
    public function structures_can_check_if_they_have_a_single_property(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertTrue($structure->hasProperty('publicStringProperty'));
        self::assertFalse($structure->hasProperty('iDontExist'));
    }

    /**
     * @test
     */
    public function structures_can_retrieve_a_single_property(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertNotNull($structure->getProperty('publicStringProperty'));
        self::assertNull($structure->getProperty('iDontExist'));
    }

    /**
     * @test
     */
    public function structures_have_a_collection_of_methods(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $methods   = $structure->getMethods();

        self::assertCount(7, $methods);
        self::assertInstanceOf(MethodCollection::class, $methods);
    }

    /**
     * @test
     */
    public function structures_can_check_if_they_have_a_single_method(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertTrue($structure->hasMethod('attributedPublicMethodWithoutParameters'));
        self::assertFalse($structure->hasMethod('iDontExist'));
    }

    /**
     * @test
     */
    public function structures_can_retrieve_a_single_method(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertNotNull($structure->getProperty('nullablePrivateIntPropertyWithDefault'));
        self::assertNull($structure->getProperty('iDontExist'));
    }

    /**
     * @test
     */
    public function structures_have_a_collection_of_metadata(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $metadata  = $structure->getAllMetadata();

        self::assertCount(3, $metadata);
        self::assertInstanceOf(MetadataCollection::class, $metadata);
    }

    /**
     * @test
     */
    public function structures_can_check_if_they_have_a_single_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertTrue($structure->hasAttribute(ClassAttribute::class));
        self::assertFalse($structure->hasAttribute(MethodAttribute::class));
    }

    /**
     * @test
     */
    public function structures_can_retrieve_a_single_piece_of_metadata(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertNotEmpty($structure->getMetadata(ClassAttribute::class));
        self::assertEmpty($structure->getMetadata(MethodAttribute::class));
    }

    /**
     * @test
     */
    public function structures_can_retrieve_the_first_piece_of_metadata_for_an_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertNotNull($structure->getFirstMetadata(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function structures_have_access_to_an_array_of_attributes(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $attributes = $structure->getAttributes();

        self::assertCount(2, $attributes);
        self::assertSame(ClassAttribute::class, $attributes[0]->getName());
    }

    /**
     * @test
     */
    public function structures_provide_their_constructor_if_they_have_one(): void
    {
        $structure   = $this->factory->makeStructure(ExampleClass::class);
        $constructor = $structure->getConstructor();

        self::assertNotNull($constructor);
        self::assertSame('__construct', $constructor->getName());
    }

    /**
     * @test
     */
    public function structures_provide_null_if_they_dont_have_a_constructor(): void
    {
        $structure = $this->factory->makeStructure(BasicInterface::class);

        self::assertNull($structure->getConstructor());
    }

    /**
     * @test
     */
    public function structures_have_a_collection_of_interfaces_they_implement(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $interfaces = $structure->getInterfaces();

        self::assertCount(2, $interfaces);
        self::assertNotNull($interfaces->get(BasicInterface::class));
        self::assertNotNull($interfaces->get(EmptyInterface::class));
    }

    /**
     * @test
     */
    public function structures_let_you_check_if_they_implement_an_interface(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertTrue($structure->implements(BasicInterface::class));
        self::assertFalse($structure->implements(
            $this->factory->makeStructure(SecondInterface::class)
        ));
    }

    /**
     * @test
     */
    public function structures_inherit_interfaces(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertTrue($structure->getInterfaces()->has(EmptyInterface::class));
    }

    /**
     * @test
     */
    public function structures_have_a_collection_of_traits_they_use(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $traits    = $structure->getTraits();

        self::assertCount(2, $traits);
        self::assertNotNull($traits->get(BasicTrait::class));
        self::assertNotNull($traits->get(EmptyTrait::class));
    }

    /**
     * @test
     */
    public function structures_let_you_check_if_they_use_a_trait(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertTrue($structure->uses(BasicTrait::class));
        self::assertFalse($structure->uses('invalid'));
        self::assertTrue($structure->uses(
            $this->factory->makeStructure(BasicTrait::class)
        ));
    }

    /**
     * @test
     */
    public function structures_inherit_traits(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertTrue($structure->getTraits()->has(EmptyTrait::class));
    }

    /**
     * @test
     */
    public function structure_collections_are_iterable(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );
        $iterator   = $collection->getIterator();

        self::assertIsIterable($collection);
        self::assertIsIterable($iterator);
    }

    /**
     * @test
     */
    public function structure_collections_let_you_retrieve_all_structures(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );
        $values     = $collection->values();

        self::assertIsArray($values);
        self::assertCount($collection->count(), $values);
    }

    /**
     * @test
     */
    public function structure_collections_let_you_retrieve_all_structures_as_a_list(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );
        $values     = $collection->values();

        self::assertTrue(array_is_list($values));
    }

    /**
     * @test
     */
    public function structure_collections_let_you_retrieve_the_first_structure(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );

        self::assertSame(ExampleClass::class, $collection->first()->getFullName());
    }

    /**
     * @test
     */
    public function structure_collections_let_you_retrieve_properties_by_their_index(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );
        $structure  = $collection->indexOf(0);

        self::assertSame(ExampleClass::class, $structure->getFullName());
    }

    /**
     * @test
     */
    public function structure_collections_return_null_for_non_existent_structure_names(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );

        self::assertNull($collection->get('iDontExist'));
    }

    /**
     * @test
     */
    public function structure_collections_return_null_for_non_existent_structure_index(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );

        self::assertNull($collection->indexOf(99));
    }

    /**
     * @test
     */
    public function structure_collections_correctly_report_their_empty_state(): void
    {
        $collection1 = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );

        $collection2 = new Structures([]);

        self::assertFalse($collection1->isEmpty());
        self::assertTrue($collection1->isNotEmpty());

        self::assertTrue($collection2->isEmpty());
        self::assertFalse($collection2->isNotEmpty());
    }

    /**
     * @test
     */
    public function structure_collections_let_you_retrieve_the_names_of_all_structures(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );
        $names      = $collection->names();

        self::assertCount($collection->count(), $names);

        foreach ($names as $name) {
            self::assertTrue($collection->has($name));
        }
    }

    /**
     * @test
     */
    public function structure_collections_let_you_retrieve_the_names_of_all_structures_as_a_list(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );
        $names      = $collection->names();

        self::assertTrue(array_is_list($names));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_a_structure_type(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->ofType(StructureType::Interface)
        ));
        self::assertCount(1, $collection->filter(
            StructureFilter::make()->ofType(StructureType::Default)
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_their_structure_types(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(3, $collection->filter(
            StructureFilter::make()->ofTypes(StructureType::Interface, StructureType::Default)
        ));
        self::assertCount(2, $collection->filter(
            StructureFilter::make()->ofTypes(StructureType::Enum, StructureType::Interface)
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_their_namespace(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->inNamespace('Smpl\Inspector\Contracts')
        ));
        self::assertCount(2, $collection->filter(
            StructureFilter::make()->notInNamespace('Smpl\Inspector\Contracts')
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_whether_they_have_a_constructor(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->hasConstructor()
        ));
        self::assertCount(2, $collection->filter(
            StructureFilter::make()->hasNoConstructor()
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_whether_they_are_instantiable(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->isInstantiable()
        ));
        self::assertCount(2, $collection->filter(
            StructureFilter::make()->isNotInstantiable()
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_their_parent(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->childOf(EmptyishClass::class)
        ));
        self::assertCount(2, $collection->filter(
            StructureFilter::make()->notChildOf(EmptyishClass::class)
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_accepted_types(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(2, $collection->filter(
            StructureFilter::make()->acceptedBy(new ClassType(BasicInterface::class))
        ));
        self::assertCount(1, $collection->filter(
            StructureFilter::make()->notAcceptedBy(new ClassType(BasicInterface::class))
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_an_interface(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->implements(BasicInterface::class)
        ));
        self::assertCount(2, $collection->filter(
            StructureFilter::make()->doesNotImplement(BasicInterface::class)
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_a_trait(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->uses(BasicTrait::class)
        ));
        self::assertCount(2, $collection->filter(
            StructureFilter::make()->doesNotUse(BasicTrait::class)
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_their_methods(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(2, $collection->filter(
            StructureFilter::make()->methodsMatch(
                MethodFilter::make()->hasReturnType()
            )
        ));
        self::assertCount(1, $collection->filter(
            StructureFilter::make()->methodsMatch(
                MethodFilter::make()->hasNoReturnType()
            )
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_their_properties(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->propertiesMatch(
                PropertyFilter::make()->static()
            )
        ));
        self::assertCount(1, $collection->filter(
            StructureFilter::make()->propertiesMatch(
                PropertyFilter::make()->notStatic()
            )
        ));
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_whether_they_have_a_specific_attribute(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class, SecondExampleClass::class
        );

        self::assertCount(1, $collection->filter(
            StructureFilter::make()->hasAttribute(ClassAttribute::class))
        );

        self::assertCount(0, $collection->filter(
            StructureFilter::make()->hasAttribute(AttributeInterface::class))
        );
    }

    /**
     * @test
     */
    public function structure_collections_can_be_filtered_by_whether_they_have_a_specific_attribute_with_instanceof_check(): void
    {
        $collection = $this->factory->makeStructures(
            ExampleClass::class, BasicInterface::class, Structure::class, SecondExampleClass::class
        );

        self::assertCount(2, $collection->filter(
            StructureFilter::make()->hasAttribute(AttributeInterface::class, true))
        );

        self::assertCount(2, $collection->filter(
            StructureFilter::make()->hasAttribute(ClassAttribute::class, true))
        );
    }
}