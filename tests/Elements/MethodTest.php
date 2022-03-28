<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Collections\Methods;
use Smpl\Inspector\Contracts\MethodCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructureMethodCollection;
use Smpl\Inspector\Elements\InheritedMethod;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Filters\MethodFilter;
use Smpl\Inspector\Filters\ParameterFilter;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;
use Smpl\Inspector\Tests\Fixtures\AbstractClass;
use Smpl\Inspector\Tests\Fixtures\BasicInterface;
use Smpl\Inspector\Tests\Fixtures\ClassAttribute;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\MethodAttribute;
use Smpl\Inspector\Types\BoolType;
use Smpl\Inspector\Types\FloatType;
use Smpl\Inspector\Types\IntType;
use Smpl\Inspector\Types\VoidType;

/**
 * @group elements
 * @group methods
 */
class MethodTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = StructureFactory::getInstance();
    }

    /**
     * @test
     */
    public function methods_know_their_declaring_structure(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertSame(
            $structure->getMethod('attributedPublicMethodWithoutParameters')->getStructure(),
            $structure->getMethod('attributedPublicMethodWithoutParameters')->getDeclaringStructure()
        );
        self::assertNotSame(
            $structure->getMethod('iAmInherited')->getStructure(),
            $structure->getMethod('iAmInherited')->getDeclaringStructure()
        );
    }

    /**
     * @test
     */
    public function methods_provide_their_short_name(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('attributedPublicMethodWithoutParameters');

        self::assertSame('attributedPublicMethodWithoutParameters', $method->getName());
    }

    /**
     * @test
     */
    public function methods_provide_their_full_name_including_class_name(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('attributedPublicMethodWithoutParameters');
        $name      = ExampleClass::class . Structure::SEPARATOR . 'attributedPublicMethodWithoutParameters';

        self::assertSame($name, $method->getFullName());
    }

    /**
     * @test
     */
    public function methods_have_the_correct_visibility(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);

        self::assertSame(
            Visibility::Public,
            $structure->getMethod('attributedPublicMethodWithoutParameters')->getVisibility()
        );
        self::assertSame(
            Visibility::Protected,
            $structure->getMethod('protectedMethodWithAParameter')->getVisibility()
        );
        self::assertSame(
            Visibility::Private,
            $structure->getMethod('privateMethodWithMultipleParameters')->getVisibility()
        );
    }

    /**
     * @test
     */
    public function methods_know_if_they_are_inherited(): void
    {
        $structure    = $this->factory->makeStructure(ExampleClass::class);
        $notInherited = $structure->getMethod('attributedPublicMethodWithoutParameters');
        $inherited    = $structure->getMethod('iAmInherited');

        self::assertTrue($inherited->isInherited());
        self::assertInstanceOf(InheritedMethod::class, $inherited);
        self::assertFalse($notInherited->isInherited());
        self::assertNotInstanceOf(InheritedMethod::class, $notInherited);
    }

    /**
     * @test
     */
    public function methods_know_if_they_are_static(): void
    {
        $structure       = $this->factory->makeStructure(AbstractClass::class);
        $staticMethod    = $structure->getMethod('staticMethod');
        $nonStaticMethod = $structure->getMethod('nonAbstractMethod');

        self::assertTrue($staticMethod->isStatic());
        self::assertFalse($nonStaticMethod->isStatic());
    }

    /**
     * @test
     */
    public function methods_know_if_they_are_abstract(): void
    {
        $structure         = $this->factory->makeStructure(AbstractClass::class);
        $abstractMethod    = $structure->getMethod('abstractMethod');
        $nonAbstractMethod = $structure->getMethod('nonAbstractMethod');

        self::assertTrue($abstractMethod->isAbstract());
        self::assertFalse($nonAbstractMethod->isAbstract());
    }

    /**
     * @test
     */
    public function methods_know_if_they_are_a_constructor(): void
    {
        $structure      = $this->factory->makeStructure(AbstractClass::class);
        $constructor    = $structure->getConstructor();
        $nonConstructor = $structure->getMethod('nonAbstractMethod');

        self::assertTrue($constructor->isConstructor());
        self::assertFalse($nonConstructor->isConstructor());
    }

    /**
     * @test
     */
    public function methods_know_their_return_type(): void
    {
        $structure     = $this->factory->makeStructure(AbstractClass::class);
        $noReturnType  = $structure->getMethod('noReturnType');
        $hasReturnType = $structure->getMethod('boolReturnType');

        self::assertNull($noReturnType->getReturnType());
        self::assertNotNull($hasReturnType->getReturnType());
        self::assertInstanceOf(BoolType::class, $hasReturnType->getReturnType());
    }

    /**
     * @test
     */
    public function methods_have_a_collection_of_parameters(): void
    {
        $structure       = $this->factory->makeStructure(ExampleClass::class);
        $noParameters    = $structure->getMethod('attributedPublicMethodWithoutParameters')->getParameters();
        $oneParameter    = $structure->getMethod('protectedMethodWithAParameter')->getParameters();
        $threeParameters = $structure->getMethod('privateMethodWithMultipleParameters')->getParameters();

        self::assertCount(0, $noParameters);
        self::assertCount(1, $oneParameter);
        self::assertCount(3, $threeParameters);
    }

    /**
     * @test
     */
    public function methods_can_filter_their_collection_of_parameters(): void
    {
        $structure     = $this->factory->makeStructure(ExampleClass::class);
        $method        = $structure->getMethod('privateMethodWithMultipleParameters');
        $allParameters = $method->getParameters();
        $parameters    = $method->getParameters(ParameterFilter::make()->hasType('int'));

        self::assertCount(3, $allParameters);
        self::assertCount(2, $parameters);
    }

    /**
     * @test
     */
    public function methods_can_check_if_they_have_a_single_parameter(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('privateMethodWithMultipleParameters');

        self::assertTrue($method->hasParameter('number2'));
    }

    /**
     * @test
     */
    public function methods_can_retrieve_a_single_parameter(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('privateMethodWithMultipleParameters');
        $parameter = $method->getParameter('number2');

        self::assertNotNull($parameter);
    }

    /**
     * @test
     */
    public function methods_can_check_if_they_have_a_single_parameter_by_index(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('privateMethodWithMultipleParameters');

        self::assertTrue($method->hasParameter(1));
    }

    /**
     * @test
     */
    public function methods_can_retrieve_a_single_parameter_by_index(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('privateMethodWithMultipleParameters');
        $parameter = $method->getParameter(1);

        self::assertNotNull($parameter);
        self::assertSame('number2', $parameter->getName());
    }

    /**
     * @test
     */
    public function methods_have_a_collection_of_metadata(): void
    {
        $structure     = $this->factory->makeStructure(ExampleClass::class);
        $oneAttribute  = $structure->getMethod('attributedPublicMethodWithoutParameters')->getAllMetadata();
        $notAttributes = $structure->getMethod('protectedMethodWithAParameter')->getAllMetadata();

        self::assertCount(1, $oneAttribute);
        self::assertCount(0, $notAttributes);
    }

    /**
     * @test
     */
    public function methods_can_check_if_they_have_a_single_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('attributedPublicMethodWithoutParameters');

        self::assertTrue($method->hasAttribute(MethodAttribute::class));
        self::assertFalse($method->hasAttribute(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function methods_can_retrieve_a_single_piece_of_metadata(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('attributedPublicMethodWithoutParameters');

        self::assertNotEmpty($method->getMetadata(MethodAttribute::class));
        self::assertEmpty($method->getMetadata(ClassAttribute::class));
    }

    /**
     * @test
     */
    public function methods_can_retrieve_the_first_piece_of_metadata_for_an_attribute(): void
    {
        $structure = $this->factory->makeStructure(ExampleClass::class);
        $method    = $structure->getMethod('attributedPublicMethodWithoutParameters');

        self::assertNotNull($method->getFirstMetadata(MethodAttribute::class));
    }

    /**
     * @test
     */
    public function methods_have_access_to_an_array_of_attributes(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $method     = $structure->getMethod('attributedPublicMethodWithoutParameters');
        $attributes = $method->getAttributes();

        self::assertCount(1, $attributes);
        self::assertSame(MethodAttribute::class, $attributes[0]->getName());
    }

    /**
     * @test
     */
    public function method_collections_are_iterable(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();
        $iterator   = $collection->getIterator();

        self::assertIsIterable($collection);
        self::assertIsIterable($iterator);
    }

    /**
     * @test
     */
    public function method_collections_let_you_retrieve_all_methods(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();
        $values     = $collection->values();

        self::assertIsArray($values);
        self::assertCount($collection->count(), $values);
    }

    /**
     * @test
     */
    public function method_collections_let_you_retrieve_all_methods_as_a_list(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();
        $values     = $collection->values();

        self::assertTrue(array_is_list($values));
    }

    /**
     * @test
     */
    public function method_collections_let_you_retrieve_the_first_method(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertSame('__construct', $collection->first()->getName());
    }

    /**
     * @test
     */
    public function method_collections_let_you_retrieve_methods_by_their_index(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();
        $method     = $collection->indexOf(0);

        self::assertSame('__construct', $method->getName());
    }

    /**
     * @test
     */
    public function method_collections_return_null_for_non_existent_method_names(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertNull($collection->get('iDontExist'));
    }

    /**
     * @test
     */
    public function method_collections_return_null_for_non_existent_method_index(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertNull($collection->indexOf(99));
    }

    /**
     * @test
     */
    public function method_collections_let_you_retrieve_methods_by_their_full_name_including_class(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();
        $method     = $collection->get(ExampleClass::class . Structure::SEPARATOR . '__construct');

        self::assertSame('__construct', $method->getName());
    }

    /**
     * @test
     */
    public function method_collections_let_you_check_if_a_method_exists_by_its_full_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertTrue($collection->has(ExampleClass::class . Structure::SEPARATOR . '__construct'));
    }

    /**
     * @test
     */
    public function method_collections_correctly_report_their_empty_state(): void
    {
        $structure1  = $this->factory->makeStructure(ExampleClass::class);
        $collection1 = $structure1->getMethods();

        $structure2  = $this->factory->makeStructure(BasicInterface::class);
        $collection2 = $structure2->getMethods();

        self::assertFalse($collection1->isEmpty());
        self::assertTrue($collection1->isNotEmpty());

        self::assertTrue($collection2->isEmpty());
        self::assertFalse($collection2->isNotEmpty());
    }

    /**
     * @test
     */
    public function method_collections_let_you_retrieve_the_full_names_of_all_methods(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();
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
    public function method_collections_let_you_retrieve_the_short_names_of_all_methods(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods()->asBase();
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
    public function method_collections_let_you_retrieve_the_short_unique_names_of_all_methods(): void
    {
        $structure1  = $this->factory->makeStructure(ExampleClass::class);
        $collection1 = $structure1->getMethods()->asBase();

        $structure2  = $this->factory->makeStructure(ExampleClass::class);
        $collection2 = $structure2->getMethods()->asBase();

        $methods = new Methods(array_merge($collection1->values(), $collection2->values()));

        self::assertCount($collection1->count(), $methods->names(false));
    }

    /**
     * @test
     */
    public function structure_method_collections_let_you_retrieve_methods_by_their_short_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();
        $method     = $collection->get('__construct');

        self::assertSame('__construct', $method->getName());
    }

    /**
     * @test
     */
    public function structure_method_collections_let_you_check_if_a_method_exists_by_its_short_name(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertTrue($collection->has('__construct'));
    }

    /**
     * @test
     */
    public function structure_method_collections_can_be_converted_to_their_base_collection(): void
    {
        $structure      = $this->factory->makeStructure(ExampleClass::class);
        $collection     = $structure->getMethods();
        $baseCollection = $collection->asBase();

        self::assertInstanceOf(MethodCollection::class, $collection);
        self::assertNotInstanceOf(StructureMethodCollection::class, $baseCollection);
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_their_visibility(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertCount(3, $collection->filter(MethodFilter::make()->publicOnly()));
        self::assertCount(3, $collection->filter(MethodFilter::make()->protectedOnly()));
        self::assertCount(1, $collection->filter(MethodFilter::make()->privateOnly()));
        self::assertCount(6, $collection->filter(MethodFilter::make()->hasVisibility(
            Visibility::Public, Visibility::Protected
        )));
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_whether_they_have_a_return_type(): void
    {
        $structure  = $this->factory->makeStructure(AbstractClass::class);
        $collection = $structure->getMethods();

        self::assertCount(2, $collection->filter(MethodFilter::make()->hasNoReturnType()));
        self::assertCount(4, $collection->filter(MethodFilter::make()->hasReturnType()));
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_their_return_type(): void
    {
        $structure  = $this->factory->makeStructure(AbstractClass::class);
        $collection = $structure->getMethods();

        self::assertCount(2, $collection->filter(
            MethodFilter::make()->hasReturnType('void')
        ));
        self::assertCount(2, $collection->filter(
            MethodFilter::make()->hasReturnType(new VoidType())
        ));
        self::assertCount(1, $collection->filter(
            MethodFilter::make()->hasReturnType('int')
        ));
        self::assertCount(1, $collection->filter(
            MethodFilter::make()->hasReturnType(new IntType())
        ));
        self::assertCount(0, $collection->filter(
            MethodFilter::make()->hasReturnType(new FloatType())
        ));
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_whether_they_are_static(): void
    {
        $structure  = $this->factory->makeStructure(AbstractClass::class);
        $collection = $structure->getMethods();

        self::assertCount(1, $collection->filter(MethodFilter::make()->static()));
        self::assertCount(5, $collection->filter(MethodFilter::make()->notStatic()));
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_whether_they_have_parameters(): void
    {
        $structure  = $this->factory->makeStructure(AbstractClass::class);
        $collection = $structure->getMethods();

        self::assertCount(1, $collection->filter(MethodFilter::make()->hasParameters()));
        self::assertCount(5, $collection->filter(MethodFilter::make()->hasNoParameters()));
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_the_number_of_parameters(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertCount(2, $collection->filter(MethodFilter::make()->parameterCount(3)));
        self::assertCount(1, $collection->filter(MethodFilter::make()->parameterCount(1)));
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_whether_they_have_a_specific_attribute(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertCount(1, $collection->filter(
            MethodFilter::make()->hasAttribute(MethodAttribute::class))
        );
    }

    /**
     * @test
     */
    public function method_collections_can_be_filtered_by_filtering_their_parameters(): void
    {
        $structure  = $this->factory->makeStructure(ExampleClass::class);
        $collection = $structure->getMethods();

        self::assertCount(2, $collection->filter(
            MethodFilter::make()->parametersMatch(
                ParameterFilter::make()->hasDefaultValue()
            )
        ));
    }
}