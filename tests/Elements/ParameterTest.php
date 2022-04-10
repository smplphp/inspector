<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\ParameterCollection;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Filters\ParameterFilter;
use Smpl\Inspector\Tests\Fixtures\ClassAttribute;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\FilterableParameterClass;
use Smpl\Inspector\Tests\Fixtures\ParameterAttribute;
use Smpl\Inspector\Tests\Fixtures\VariadicMethodInterface;
use Smpl\Inspector\Types\BoolType;
use Smpl\Inspector\Types\FloatType;
use Smpl\Inspector\Types\IntType;
use Smpl\Inspector\Types\NullableType;
use Smpl\Inspector\Types\StringType;

/**
 * @group elements
 * @group parameters
 */
class ParameterTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = StructureFactory::getInstance();
    }

    /**
     * @test
     */
    public function parameters_know_the_method_they_belong_to(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getConstructor();
        $parameter = $method->getParameters()->first();

        self::assertSame($method, $parameter->getMethod());
    }

    /**
     * @test
     */
    public function parameters_have_the_correct_name(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getConstructor();
        $parameter = $method->getParameters()->first();

        self::assertSame('someString', $parameter->getName());
    }

    /**
     * @test
     */
    public function parameters_know_if_they_are_promoted(): void
    {
        $structure   = $this->factory->makeStructure(ExampleClass::class);
        $method      = $structure->getConstructor();
        $notPromoted = $method->getParameters()->first();
        $promoted    = $method->getParameter('promotedPublicBoolProperty');

        self::assertFalse($notPromoted->isPromoted());
        self::assertTrue($promoted->isPromoted());
        self::assertNull($notPromoted->getProperty());
        self::assertNotNull($promoted->getProperty());
    }

    /**
     * @test
     */
    public function parameters_know_if_they_are_nullable(): void
    {
        $structure   = $this->factory->makeStructure(ExampleClass::class);
        $method      = $structure->getMethod('privateMethodWithMultipleParameters');
        $nullable    = $method->getParameter('number2');
        $notNullable = $method->getParameter('number1');

        self::assertTrue($nullable->isNullable());
        self::assertFalse($notNullable->isNullable());
    }

    /**
     * @test
     */
    public function parameters_know_if_they_have_a_default_value(): void
    {
        $structure    = $this->factory->makeStructure(ExampleClass::class);
        $method       = $structure->getMethod('privateMethodWithMultipleParameters');
        $hasDefault   = $method->getParameter('string');
        $hasNoDefault = $method->getParameter('number1');

        self::assertTrue($hasDefault->hasDefault());
        self::assertFalse($hasNoDefault->hasDefault());
    }

    /**
     * @test
     */
    public function parameters_know_their_default_value(): void
    {
        $structure    = $this->factory->makeStructure(ExampleClass::class);
        $method       = $structure->getMethod('privateMethodWithMultipleParameters');
        $hasDefault   = $method->getParameter('string');
        $hasNoDefault = $method->getParameter('number1');

        self::assertSame('result', $hasDefault->getDefault());
        self::assertNull($hasNoDefault->getDefault());
    }

    /**
     * @test
     */
    public function parameters_know_if_they_are_variadic(): void
    {
        $structure     = $this->factory->makeStructure(VariadicMethodInterface::class);
        $method        = $structure->getMethod('testFunction');
        $isVariadic    = $method->getParameter('variadic');
        $isNotVariadic = $method->getParameter('number');

        self::assertTrue($isVariadic->isVariadic());
        self::assertFalse($isNotVariadic->isVariadic());
    }

    /**
     * @test
     */
    public function parameters_have_a_collection_of_metadata(): void
    {
        $structure     = $this->factory->makeStructure(ExampleClass::class);
        $method        = $structure->getConstructor();
        $oneAttribute  = $method->getParameter('someNumber')->getAllMetadata();
        $notAttributes = $method->getParameter('someString')->getAllMetadata();

        self::assertCount(1, $oneAttribute);
        self::assertCount(0, $notAttributes);
    }

    /**
     * @test
     */
    public function parameters_can_check_if_they_have_a_single_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getConstructor();
        $parameter = $method->getParameter('someNumber');

        self::assertTrue($parameter->hasAttribute(ParameterAttribute::class));
        self::assertFalse($parameter->hasAttribute(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function parameters_can_retrieve_a_single_piece_of_metadata(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getConstructor();
        $parameter = $method->getParameter('someNumber');

        self::assertNotEmpty($parameter->getMetadata(ParameterAttribute::class));
        self::assertEmpty($parameter->getMetadata(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function parameters_can_retrieve_the_first_piece_of_metadata_for_an_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getConstructor();
        $parameter = $method->getParameter('someNumber');

        self::assertNotNull($parameter->getFirstMetadata(ParameterAttribute::class));
    }

    /**
     * @test
     */
    public function parameters_have_access_to_an_array_of_attributes(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $method     = $structure->getConstructor();
        $parameter  = $method->getParameter('someNumber');
        $attributes = $parameter->getAttributes();

        self::assertCount(1, $attributes);
        self::assertSame(ParameterAttribute::class, $attributes[0]->getName());
    }

    /**
     * @test
     */
    public function parameter_collections_are_iterable(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();
        $iterator   = $collection->getIterator();

        self::assertIsIterable($collection);
        self::assertIsIterable($iterator);
    }

    /**
     * @test
     */
    public function parameter_collections_let_you_retrieve_all_parameters(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();
        $values     = $collection->values();

        self::assertIsArray($values);
        self::assertCount($collection->count(), $values);
    }

    /**
     * @test
     */
    public function parameter_collections_let_you_retrieve_all_parameters_as_a_list(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();
        $values     = $collection->filter(
            ParameterFilter::make()->hasDefaultValue()
        )->values();

        self::assertTrue(array_is_list($values));
        self::assertSame('someNumber', $values[0]->getName());
        self::assertSame('promotedPublicBoolProperty', $values[1]->getName());
    }

    /**
     * @test
     */
    public function parameter_collections_let_you_retrieve_the_first_parameter(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertSame('someString', $collection->first()->getName());
    }

    /**
     * @test
     */
    public function parameter_collections_let_you_retrieve_parameters_by_their_index(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();
        $parameter1 = $collection->indexOf(0);
        $parameter2 = $collection->filter(
            ParameterFilter::make()->hasType('int')
        )->indexOf(1);

        self::assertSame('someString', $parameter1->getName());
        self::assertSame('someNumber', $parameter2->getName());
    }

    /**
     * @test
     */
    public function parameter_collections_let_you_retrieve_parameters_by_their_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();
        $parameter  = $collection->get('someString');

        self::assertSame('someString', $parameter->getName());
    }

    /**
     * @test
     */
    public function parameter_collections_return_null_for_non_existent_parameter_names(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertNull($collection->get('iDontExist'));
    }

    /**
     * @test
     */
    public function parameter_collections_return_null_for_non_existent_parameter_index(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertNull($collection->indexOf(99));
    }

    /**
     * @test
     */
    public function parameter_collections_let_you_check_if_a_parameter_exists_by_its_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertTrue($collection->has('someString'));
    }

    /**
     * @test
     */
    public function parameter_collections_correctly_report_their_empty_state(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertFalse($collection->isEmpty());
        self::assertTrue($collection->isNotEmpty());
    }

    /**
     * @test
     */
    public function parameter_collections_let_you_retrieve_the_names_of_all_parameters(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getConstructor()->getParameters();
        $names      = $collection->names();

        self::assertCount($collection->count(), $names);

        foreach ($names as $name) {
            self::assertTrue($collection->has($name));
        }
    }

    /**
     * @test
     */
    public function parameter_collections_return_names_ordered_by_position(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $reflection = $structure->getConstructor()->getReflection();
        $parameters = $reflection->getParameters();
        shuffle($parameters);
        $collection = $this->factory->makeParameters(...$parameters);
        $names      = $collection->filter(
            ParameterFilter::make()->hasDefaultValue()
        )->names();

        self::assertCount(2, $names);
        self::assertSame($names[0], 'someNumber');
        self::assertSame($names[1], 'promotedPublicBoolProperty');
    }

    /**
     * @test
     */
    public function method_parameter_collections_can_be_converted_to_their_base_collection(): void
    {
        $structure      = $this->factory->makeStructure(ExampleClass::class);
        $collection     = $structure->getConstructor()->getParameters();
        $baseCollection = $collection->asBase();

        self::assertInstanceOf(ParameterCollection::class, $collection);
        self::assertNotInstanceOf(MethodParameterCollection::class, $baseCollection);
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_whether_they_have_a_type(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(2, $collection->filter(ParameterFilter::make()->notTyped()));
        self::assertCount(4, $collection->filter(ParameterFilter::make()->typed()));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_their_type(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(1, $collection->filter(
            ParameterFilter::make()->hasType('string')
        ));
        self::assertCount(1, $collection->filter(
            ParameterFilter::make()->hasType(new StringType())
        ));
        self::assertCount(1, $collection->filter(
            ParameterFilter::make()->hasType('int')
        ));
        self::assertCount(1, $collection->filter(
            ParameterFilter::make()->hasType(new IntType())
        ));
        self::assertCount(1, $collection->filter(
            ParameterFilter::make()->hasType('?bool')
        ));
        self::assertCount(1, $collection->filter(
            ParameterFilter::make()->hasType(new NullableType(new BoolType()))
        ));
        self::assertCount(0, $collection->filter(
            ParameterFilter::make()->hasType('float')
        ));
        self::assertCount(0, $collection->filter(
            ParameterFilter::make()->hasType(new FloatType())
        ));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_what_their_type_accepts(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeAccepts('string')
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeAccepts(new StringType())
        ));
        self::assertCount(4, $collection->filter(
            ParameterFilter::make()->typeAccepts('int')
        ));
        self::assertCount(4, $collection->filter(
            ParameterFilter::make()->typeAccepts(new IntType())
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeAccepts('?bool')
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeAccepts(new NullableType(new BoolType()))
        ));
        self::assertCount(2, $collection->filter(
            ParameterFilter::make()->typeAccepts('float')
        ));
        self::assertCount(2, $collection->filter(
            ParameterFilter::make()->typeAccepts(new FloatType())
        ));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_what_their_type_does_not_accept(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept('string')
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept(new StringType())
        ));
        self::assertCount(2, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept('int')
        ));
        self::assertCount(2, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept(new IntType())
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept('?bool')
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept(new NullableType(new BoolType()))
        ));
        self::assertCount(4, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept('float')
        ));
        self::assertCount(4, $collection->filter(
            ParameterFilter::make()->typeDoesNotAccept(new FloatType())
        ));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_what_their_type_matches(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeMatches('string')
        ));
        self::assertCount(4, $collection->filter(
            ParameterFilter::make()->typeMatches(3)
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeMatches(false)
        ));
        self::assertCount(4, $collection->filter(
            ParameterFilter::make()->typeMatches(null)
        ));
        self::assertCount(2, $collection->filter(
            ParameterFilter::make()->typeMatches(11.6)
        ));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_what_their_type_does_not_match(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeDoesNotMatch('string')
        ));
        self::assertCount(2, $collection->filter(
            ParameterFilter::make()->typeDoesNotMatch(3)
        ));
        self::assertCount(3, $collection->filter(
            ParameterFilter::make()->typeDoesNotMatch(false)
        ));
        self::assertCount(2, $collection->filter(
            ParameterFilter::make()->typeDoesNotMatch(null)
        ));
        self::assertCount(4, $collection->filter(
            ParameterFilter::make()->typeDoesNotMatch(11.6)
        ));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_whether_they_are_nullable(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(4, $collection->filter(ParameterFilter::make()->nullable()));
        self::assertCount(2, $collection->filter(ParameterFilter::make()->notNullable()));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_whether_they_have_a_default_value(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(4, $collection->filter(ParameterFilter::make()->hasDefaultValue()));
        self::assertCount(2, $collection->filter(ParameterFilter::make()->noDefaultValue()));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_whether_they_are_promoted(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(1, $collection->filter(ParameterFilter::make()->promoted()));
        self::assertCount(5, $collection->filter(ParameterFilter::make()->notPromoted()));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_whether_they_are_variadic(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getMethod('variadicParameters')->getParameters();

        self::assertCount(1, $collection->filter(ParameterFilter::make()->variadic()));
        self::assertCount(2, $collection->filter(ParameterFilter::make()->notVariadic()));
    }

    /**
     * @test
     */
    public function parameter_collections_can_be_filtered_by_whether_they_have_a_specific_attribute(): void
    {
        $structure  = $this->factory->makeStructure(FilterableParameterClass::class);
        $collection = $structure->getConstructor()->getParameters();

        self::assertCount(1, $collection->filter(
            ParameterFilter::make()->hasAttribute(ParameterAttribute::class))
        );
    }
}