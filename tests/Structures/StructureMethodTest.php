<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Structures;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Exceptions\StructureException;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Filters\MethodFilter;
use Smpl\Inspector\Filters\ParameterFilter;
use Smpl\Inspector\Support\Visibility;
use Smpl\Inspector\Tests\Fixtures\MethodParameterClass;
use Smpl\Inspector\Tests\Fixtures\TypeReflectableClass;
use Smpl\Inspector\Types\IntType;
use Smpl\Inspector\Types\StringType;
use Smpl\Inspector\Types\UnionType;
use Smpl\Inspector\Types\VoidType;
use Traversable;

/**
 * @group factories
 * @group structures
 */
class StructureMethodTest extends TestCase
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
    public function correctly_loads_methods_for_classes(): void
    {
        $structure = $this->factory->makeStructure(TypeReflectableClass::class);
        $methods   = $structure->getMethods();

        self::assertCount(8, $methods);
    }

    /**
     * @test
     */
    public function structure_methods_are_accurate(): void
    {
        $structure = $this->factory->makeStructure(TypeReflectableClass::class);
        $methods   = $structure->getMethods();

        self::assertTrue($methods->has('voidReturn'));
        self::assertTrue($methods->has('privateMethod'));
        self::assertTrue($methods->has('protectedMethod'));
        self::assertTrue($methods->has('publicMethodInt'));
        self::assertTrue($methods->has('protectedMethodString'));
        self::assertTrue($methods->has('publicMethodWithParameter'));
        self::assertTrue($methods->has('publicMethodWithParameters'));
        self::assertTrue($methods->has('anAbstractFunction'));

        self::assertSame($structure, $methods->get('voidReturn')->getStructure());
        self::assertSame($structure, $methods->get('privateMethod')->getStructure());
        self::assertSame($structure, $methods->get('protectedMethod')->getStructure());
        self::assertSame($structure, $methods->get('publicMethodInt')->getStructure());
        self::assertSame($structure, $methods->get('protectedMethodString')->getStructure());
        self::assertSame($structure, $methods->get('publicMethodWithParameter')->getStructure());
        self::assertSame($structure, $methods->get('publicMethodWithParameters')->getStructure());
        self::assertSame($structure, $methods->get('anAbstractFunction')->getStructure());

        self::assertEquals('voidReturn', $methods->get('voidReturn')->getName());
        self::assertEquals('privateMethod', $methods->get('privateMethod')->getName());
        self::assertEquals('protectedMethod', $methods->get('protectedMethod')->getName());
        self::assertEquals('publicMethodInt', $methods->get('publicMethodInt')->getName());
        self::assertEquals('protectedMethodString', $methods->get('protectedMethodString')->getName());
        self::assertEquals('publicMethodWithParameter', $methods->get('publicMethodWithParameter')->getName());
        self::assertEquals('publicMethodWithParameters', $methods->get('publicMethodWithParameters')->getName());
        self::assertEquals('anAbstractFunction', $methods->get('anAbstractFunction')->getName());

        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::voidReturn',
            $methods->get('voidReturn')->getFullName()
        );
        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::privateMethod',
            $methods->get('privateMethod')->getFullName()
        );
        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::protectedMethod',
            $methods->get('protectedMethod')->getFullName()
        );
        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::publicMethodInt',
            $methods->get('publicMethodInt')->getFullName()
        );
        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::protectedMethodString',
            $methods->get('protectedMethodString')->getFullName()
        );
        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::publicMethodWithParameter',
            $methods->get('publicMethodWithParameter')->getFullName()
        );
        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::publicMethodWithParameters',
            $methods->get('publicMethodWithParameters')->getFullName()
        );
        self::assertEquals(
            'Smpl\Inspector\Tests\Fixtures\TypeReflectableClass::anAbstractFunction',
            $methods->get('anAbstractFunction')->getFullName()
        );

        self::assertFalse($methods->get('voidReturn')->isStatic());
        self::assertFalse($methods->get('privateMethod')->isStatic());
        self::assertFalse($methods->get('protectedMethod')->isStatic());
        self::assertFalse($methods->get('publicMethodInt')->isStatic());
        self::assertFalse($methods->get('protectedMethodString')->isStatic());
        self::assertFalse($methods->get('publicMethodWithParameter')->isStatic());
        self::assertTrue($methods->get('publicMethodWithParameters')->isStatic());
        self::assertFalse($methods->get('anAbstractFunction')->isStatic());

        self::assertFalse($methods->get('voidReturn')->isAbstract());
        self::assertFalse($methods->get('privateMethod')->isAbstract());
        self::assertFalse($methods->get('protectedMethod')->isAbstract());
        self::assertFalse($methods->get('publicMethodInt')->isAbstract());
        self::assertFalse($methods->get('protectedMethodString')->isAbstract());
        self::assertFalse($methods->get('publicMethodWithParameter')->isAbstract());
        self::assertFalse($methods->get('publicMethodWithParameters')->isAbstract());
        self::assertTrue($methods->get('anAbstractFunction')->isAbstract());

        self::assertSame(Visibility::Public, $methods->get('voidReturn')->getVisibility());
        self::assertSame(Visibility::Private, $methods->get('privateMethod')->getVisibility());
        self::assertSame(Visibility::Protected, $methods->get('protectedMethod')->getVisibility());
        self::assertSame(Visibility::Public, $methods->get('publicMethodInt')->getVisibility());
        self::assertSame(Visibility::Protected, $methods->get('protectedMethodString')->getVisibility());
        self::assertSame(Visibility::Public, $methods->get('publicMethodWithParameter')->getVisibility());
        self::assertSame(Visibility::Public, $methods->get('publicMethodWithParameters')->getVisibility());
        self::assertSame(Visibility::Protected, $methods->get('anAbstractFunction')->getVisibility());

        self::assertInstanceOf(VoidType::class, $methods->get('voidReturn')->getReturnType());
        self::assertNull($methods->get('privateMethod')->getReturnType());
        self::assertInstanceOf(VoidType::class, $methods->get('protectedMethod')->getReturnType());
        self::assertInstanceOf(IntType::class, $methods->get('publicMethodInt')->getReturnType());
        self::assertInstanceOf(StringType::class, $methods->get('protectedMethodString')->getReturnType());
        self::assertInstanceOf(UnionType::class, $methods->get('publicMethodWithParameter')->getReturnType());
        self::assertInstanceOf(UnionType::class, $methods->get('publicMethodWithParameters')->getReturnType());
        self::assertInstanceOf(VoidType::class, $methods->get('anAbstractFunction')->getReturnType());
    }

    /**
     * @test
     */
    public function structure_method_collections_are_iterable(): void
    {
        $structure = $this->factory->makeStructure(TypeReflectableClass::class);
        $methods   = $structure->getMethods();

        self::assertIsIterable($methods);
        self::assertInstanceOf(Traversable::class, $methods->getIterator());
    }

    /**
     * @test
     */
    public function structure_method_collections_belong_to_their_parent_structure(): void
    {
        $structure = $this->factory->makeStructure(TypeReflectableClass::class);
        $methods   = $structure->getMethods();

        self::assertSame($structure, $methods->getStructure());
    }

    /**
     * @test
     */
    public function can_filter_methods_by_their_static_modifier(): void
    {
        $structure        = $this->factory->makeStructure(TypeReflectableClass::class);
        $staticMethods    = $structure->getMethods()->filter(MethodFilter::make()->static());
        $nonStaticMethods = $structure->getMethods()->filter(MethodFilter::make()->notStatic());

        self::assertCount(1, $staticMethods);
        self::assertCount(7, $nonStaticMethods);
    }

    /**
     * @test
     */
    public function can_filter_methods_by_their_visibility(): void
    {
        $structure        = $this->factory->makeStructure(TypeReflectableClass::class);
        $publicMethods    = $structure->getMethods()->filter(MethodFilter::make()->publicOnly());
        $protectedMethods = $structure->getMethods()->filter(MethodFilter::make()->protectedOnly());
        $privateMethods   = $structure->getMethods()->filter(MethodFilter::make()->privateOnly());

        self::assertCount(4, $publicMethods);
        self::assertCount(3, $protectedMethods);
        self::assertCount(1, $privateMethods);

        $publicMethods2    = $structure->getMethods()->filter(
            MethodFilter::make()->hasVisibility(Visibility::Public)
        );
        $protectedMethods2 = $structure->getMethods()->filter(
            MethodFilter::make()->hasVisibility(Visibility::Protected)
        );
        $privateMethods2   = $structure->getMethods()->filter(
            MethodFilter::make()->hasVisibility(Visibility::Private)
        );

        self::assertCount(4, $publicMethods2);
        self::assertCount(3, $protectedMethods2);
        self::assertCount(1, $privateMethods2);
    }

    /**
     * @test
     */
    public function can_filter_methods_by_whether_they_have_a_type(): void
    {
        $structure       = $this->factory->makeStructure(TypeReflectableClass::class);
        $typedMethods    = $structure->getMethods()->filter(MethodFilter::make()->hasReturnType());
        $notTypedMethods = $structure->getMethods()->filter(MethodFilter::make()->hasNoReturnType());

        self::assertCount(7, $typedMethods);
        self::assertCount(1, $notTypedMethods);
    }

    /**
     * @test
     */
    public function can_filter_methods_by_number_of_parameters(): void
    {
        $structure    = $this->factory->makeStructure(TypeReflectableClass::class);
        $noParameters = $structure->getMethods()->filter(MethodFilter::make()->parameterCount(0));
        $oneParameter = $structure->getMethods()->filter(MethodFilter::make()->parameterCount(1));

        self::assertCount(6, $noParameters);
        self::assertCount(1, $oneParameter);
    }

    /**
     * @test
     */
    public function can_filter_methods_by_their_type(): void
    {
        $structure     = $this->factory->makeStructure(TypeReflectableClass::class);
        $stringMethods = $structure->getMethods()->filter(MethodFilter::make()->hasReturnType('string'));
        $voidMethods   = $structure->getMethods()->filter(MethodFilter::make()->hasReturnType('void'));

        self::assertCount(1, $stringMethods);
        self::assertCount(3, $voidMethods);
    }

    /**
     * @test
     */
    public function can_filter_methods_by_parameter_presence(): void
    {
        $structure    = $this->factory->makeStructure(TypeReflectableClass::class);
        $noParameters = $structure->getMethods()->filter(MethodFilter::make()->hasNoParameters());
        $parameters   = $structure->getMethods()->filter(MethodFilter::make()->hasParameters());

        self::assertCount(6, $noParameters);
        self::assertCount(2, $parameters);
    }

    /**
     * @test
     */
    public function can_get_method_from_structure_reflection(): void
    {
        $structure = $this->factory->makeStructure(TypeReflectableClass::class);
        $method    = $this->factory->makeMethod('voidReturn', $structure);

        self::assertNotNull($method);
    }

    /**
     * @test
     */
    public function can_get_parameters_for_methods(): void
    {
        $structure  = $this->factory->makeStructure(TypeReflectableClass::class);
        $method     = $structure->getMethods()->get('publicMethodWithParameter');
        $parameters = $method->getParameters();

        self::assertCount(1, $parameters);
    }

    /**
     * @test
     */
    public function accurately_represents_method_parameters(): void
    {
        $structure  = $this->factory->makeStructure(TypeReflectableClass::class);
        $method     = $structure->getMethods()->get('publicMethodWithParameter');
        $parameters = $method->getParameters();

        self::assertCount(1, $parameters);
        self::assertNotNull($parameters->get(0));
        self::assertNotNull($parameters->get('param'));
        self::assertInstanceOf(IntType::class, $parameters->get(0)->getType());
        self::assertFalse($parameters->get(0)->isNullable());
        self::assertFalse($parameters->get(0)->isPromoted());
        self::assertFalse($parameters->get(0)->isVariadic());
        self::assertTrue($parameters->get(0)->hasDefault());
        self::assertSame(1, $parameters->get(0)->getDefault());
        self::assertSame($method, $parameters->get(0)->getMethod());
        self::assertSame($method, $parameters->getMethod());
    }

    /**
     * @test
     */
    public function accurately_represents_promoted_parameters(): void
    {
        $structure  = $this->factory->makeStructure(MethodParameterClass::class);
        $method     = $structure->getMethods()->get('__construct');
        $parameters = $method->getParameters();

        self::assertCount(2, $parameters);
        self::assertTrue($parameters->has(0));
        self::assertTrue($parameters->has('promoted'));
        self::assertNotNull($parameters->get(0));
        self::assertNotNull($parameters->get('promoted'));
        self::assertTrue($parameters->get(0)->isPromoted());
        self::assertNotNull($parameters->get(0)->getProperty());
        self::assertInstanceOf(StringType::class, $parameters->get(0)->getType());
        self::assertFalse($parameters->get(1)->isNullable());
        self::assertFalse($parameters->get(1)->isPromoted());
        self::assertFalse($parameters->get(1)->isVariadic());
        self::assertFalse($parameters->get(1)->hasDefault());
        self::assertNull($parameters->get(1)->getProperty());
    }

    /**
     * @test
     */
    public function can_get_a_method_parameter_by_position(): void
    {
        $structure   = $this->factory->makeStructure(TypeReflectableClass::class);
        $method      = $structure->getMethods()->get('publicMethodWithParameter');
        $parameter   = $method->getParameters()->get(0);
        $noParameter = $method->getParameters()->get(1);

        self::assertNull($noParameter);
        self::assertNotNull($parameter);
        self::assertSame('param', $parameter->getName());
        self::assertTrue($method->getParameters()->has(0));
        self::assertFalse($method->getParameters()->has(1));
    }

    /**
     * @test
     */
    public function can_get_a_method_parameter_by_name(): void
    {
        $structure   = $this->factory->makeStructure(TypeReflectableClass::class);
        $method      = $structure->getMethods()->get('publicMethodWithParameter');
        $parameter   = $method->getParameters()->get('param');
        $noParameter = $method->getParameters()->get('noParam');

        self::assertNull($noParameter);
        self::assertNotNull($parameter);
        self::assertSame(0, $parameter->getPosition());
        self::assertTrue($method->getParameters()->has('param'));
        self::assertFalse($method->getParameters()->has('noParam'));
    }

    /**
     * @test
     */
    public function method_parameter_collections_are_iterable(): void
    {
        $structure  = $this->factory->makeStructure(TypeReflectableClass::class);
        $parameters = $structure->getMethods()->get('publicMethodWithParameter')->getParameters();

        self::assertIsIterable($parameters);
        self::assertInstanceOf(Traversable::class, $parameters->getIterator());
    }

    /**
     * @test
     */
    public function can_filter_method_parameters_by_whether_they_have_a_type(): void
    {
        $structure = $this->factory->makeStructure(MethodParameterClass::class);
        $method    = $structure->getMethods()->get('someParameters');
        $typed     = $method->getParameters()->filter(ParameterFilter::make()->typed());
        $notTyped  = $method->getParameters()->filter(ParameterFilter::make()->notTyped());

        self::assertCount(4, $typed);
        self::assertCount(1, $notTyped);
    }

    /**
     * @test
     */
    public function can_filter_method_parameters_by_their_type(): void
    {
        $structure  = $this->factory->makeStructure(MethodParameterClass::class);
        $method     = $structure->getMethods()->get('someParameters');
        $intType    = $method->getParameters()->filter(ParameterFilter::make()->hasType('int'));
        $stringType = $method->getParameters()->filter(ParameterFilter::make()->hasType('string'));

        self::assertCount(1, $intType);
        self::assertCount(2, $stringType);
    }

    /**
     * @test
     */
    public function can_filter_method_parameters_by_their_nullability(): void
    {
        $structure   = $this->factory->makeStructure(MethodParameterClass::class);
        $method      = $structure->getMethods()->get('someParameters');
        $nullable    = $method->getParameters()->filter(ParameterFilter::make()->nullable());
        $notNullable = $method->getParameters()->filter(ParameterFilter::make()->notNullable());

        self::assertCount(2, $nullable);
        self::assertCount(3, $notNullable);
    }

    /**
     * @test
     */
    public function can_filter_method_parameters_by_whether_they_have_a_default_value(): void
    {
        $structure      = $this->factory->makeStructure(MethodParameterClass::class);
        $method         = $structure->getMethods()->get('someParameters');
        $defaultValue   = $method->getParameters()->filter(ParameterFilter::make()->hasDefaultValue());
        $noDefaultValue = $method->getParameters()->filter(ParameterFilter::make()->noDefaultValue());

        self::assertCount(1, $defaultValue);
        self::assertCount(4, $noDefaultValue);
    }

    /**
     * @test
     */
    public function can_filter_method_parameters_by_whether_they_are_variadic(): void
    {
        $structure   = $this->factory->makeStructure(MethodParameterClass::class);
        $method      = $structure->getMethods()->get('someParameters');
        $variadic    = $method->getParameters()->filter(ParameterFilter::make()->variadic());
        $notVariadic = $method->getParameters()->filter(ParameterFilter::make()->notVariadic());

        self::assertCount(1, $variadic);
        self::assertCount(4, $notVariadic);
    }

    /**
     * @test
     */
    public function can_filter_method_parameters_by_whether_they_are_promoted(): void
    {
        $structure   = $this->factory->makeStructure(MethodParameterClass::class);
        $method      = $structure->getMethods()->get('__construct');
        $promoted    = $method->getParameters()->filter(ParameterFilter::make()->promoted());
        $notPromoted = $method->getParameters()->filter(ParameterFilter::make()->notPromoted());

        self::assertCount(1, $promoted);
        self::assertCount(1, $notPromoted);
    }

    /**
     * @test
     */
    public function can_get_method_parameters_from_class_and_method_as_strings(): void
    {
        $parameters = $this->factory->makeParameters('__construct', MethodParameterClass::class);

        self::assertTrue(true);
    }

    /**
     * @test
     */
    public function throws_an_exception_when_getting_method_parameters_from_method_string_without_a_class(): void
    {
        $this->expectException(StructureException::class);
        $this->expectExceptionMessage('No class or structure was provided when attempting to retrieve parameters for \'__construct\'');

        $this->factory->makeParameters('__construct');
    }

    /**
     * @test
     */
    public function methods_know_if_they_are_a_constructor(): void
    {
        $structure = $this->factory->makeStructure(MethodParameterClass::class);
        $method1   = $structure->getMethods()->get('__construct');
        $method2   = $structure->getMethods()->get('someParameters');

        self::assertTrue($method1->isConstructor());
        self::assertFalse($method2->isConstructor());
    }
}