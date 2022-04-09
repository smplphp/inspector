<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type as SebastianBergmann;
use Smpl\Inspector\Concerns\HasAttributes;
use Smpl\Inspector\Elements\Attribute;
use Smpl\Inspector\Elements\InheritedMethod;
use Smpl\Inspector\Elements\InheritedProperty;
use Smpl\Inspector\Elements\Metadata;
use Smpl\Inspector\Elements\Method;
use Smpl\Inspector\Elements\Parameter;
use Smpl\Inspector\Elements\Property;
use Smpl\Inspector\Elements\Structure;
use Smpl\Inspector\Exceptions\InspectionException;
use Smpl\Inspector\Filters\MethodFilter;
use Smpl\Inspector\Filters\ParameterFilter;
use Smpl\Inspector\Filters\PropertyFilter;
use Smpl\Inspector\Filters\StructureFilter;
use Smpl\Inspector\Inspection;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Mappers\ClassmapMapper;
use Smpl\Inspector\Mappers\PSR4Mapper;
use Smpl\Inspector\Support\Visibility;
use TheSeer\Tokenizer;

/**
 * @group inspection
 */
class InspectionTest extends TestCase
{
    protected function setUp(): void
    {
        Inspector::setInstance(new Inspector(mapper: new PSR4Mapper(require './vendor/composer/autoload_psr4.php')));
    }

    /**
     * @test
     */
    public function can_inspect_individual_classes(): void
    {
        $inspection = Inspector::getInstance()->inspectClass(Inspector::class);

        self::assertNotNull($inspection);
        self::assertEquals(Inspector::class, $inspection->getFullName());
    }

    /**
     * @test
     */
    public function can_inspect_multiple_classes(): void
    {
        $classes = Inspector::getInstance()
                            ->inspect()
                            ->inClasses(Inspection::class, Inspector::class)
                            ->getStructures()
                            ->names();

        self::assertCount(2, $classes);
        self::assertContains(Inspection::class, $classes);
        self::assertContains(Inspector::class, $classes);
    }

    /**
     * @test
     */
    public function can_inspect_individual_methods(): void
    {
        $inspection = Inspector::getInstance()->inspectMethod(Inspector::class, 'getInstance');

        self::assertNotNull($inspection);
        self::assertEquals('getInstance', $inspection->getName());
        self::assertEquals(Inspector::class . Structure::SEPARATOR . 'getInstance', $inspection->getFullName());
    }

    /**
     * @test
     */
    public function can_inspect_structures_in_a_path(): void
    {
        $structures = Inspector::getInstance()
                               ->inspect()
                               ->inPath('./src/Elements')
                               ->getStructures();

        self::assertCount(8, $structures);
        self::assertTrue($structures->has(Attribute::class));
        self::assertTrue($structures->has(InheritedMethod::class));
        self::assertTrue($structures->has(InheritedProperty::class));
        self::assertTrue($structures->has(Metadata::class));
        self::assertTrue($structures->has(Method::class));
        self::assertTrue($structures->has(Parameter::class));
        self::assertTrue($structures->has(Property::class));
        self::assertTrue($structures->has(Structure::class));
    }

    /**
     * @test
     */
    public function can_inspect_structures_in_a_namespace(): void
    {
        $structures = Inspector::getInstance()
                               ->inspect()
                               ->inNamespace('Smpl\Inspector\Elements')
                               ->getStructures();

        self::assertCount(8, $structures);
        self::assertTrue($structures->has(Attribute::class));
        self::assertTrue($structures->has(InheritedMethod::class));
        self::assertTrue($structures->has(InheritedProperty::class));
        self::assertTrue($structures->has(Metadata::class));
        self::assertTrue($structures->has(Method::class));
        self::assertTrue($structures->has(Parameter::class));
        self::assertTrue($structures->has(Property::class));
        self::assertTrue($structures->has(Structure::class));
    }

    /**
     * @test
     */
    public function can_inspect_structures_in_multiple_paths(): void
    {
        $structures = Inspector::getInstance()
                               ->inspect()
                               ->inPaths('./src/Elements', './src/Filters')
                               ->getStructures();

        self::assertCount(12, $structures);
        self::assertTrue($structures->has(Attribute::class));
        self::assertTrue($structures->has(InheritedMethod::class));
        self::assertTrue($structures->has(InheritedProperty::class));
        self::assertTrue($structures->has(Metadata::class));
        self::assertTrue($structures->has(Method::class));
        self::assertTrue($structures->has(Parameter::class));
        self::assertTrue($structures->has(Property::class));
        self::assertTrue($structures->has(Structure::class));
        self::assertTrue($structures->has(MethodFilter::class));
        self::assertTrue($structures->has(ParameterFilter::class));
        self::assertTrue($structures->has(PropertyFilter::class));
        self::assertTrue($structures->has(StructureFilter::class));
    }

    /**
     * @test
     */
    public function can_inspect_structures_in_multiple_namespace(): void
    {
        $structures = Inspector::getInstance()
                               ->inspect()
                               ->inNamespaces('Smpl\Inspector\Elements', 'Smpl\Inspector\Filters')
                               ->getStructures();

        self::assertCount(12, $structures);
        self::assertTrue($structures->has(Attribute::class));
        self::assertTrue($structures->has(InheritedMethod::class));
        self::assertTrue($structures->has(InheritedProperty::class));
        self::assertTrue($structures->has(Metadata::class));
        self::assertTrue($structures->has(Method::class));
        self::assertTrue($structures->has(Parameter::class));
        self::assertTrue($structures->has(Property::class));
        self::assertTrue($structures->has(Structure::class));
        self::assertTrue($structures->has(MethodFilter::class));
        self::assertTrue($structures->has(ParameterFilter::class));
        self::assertTrue($structures->has(PropertyFilter::class));
        self::assertTrue($structures->has(StructureFilter::class));
    }

    /**
     * @test
     */
    public function can_inspect_paths_using_a_custom_mapper(): void
    {
        $structures = Inspector::getInstance()
                               ->inspect()
                               ->inPaths(
                                   './vendor/theseer/tokenizer/src',
                                   './vendor/sebastian/type/src/'
                               )
                               ->usingMapper(
                                   new ClassmapMapper(require './vendor/composer/autoload_classmap.php')
                               )
                               ->getStructures();

        self::assertCount(27, $structures);
        self::assertTrue($structures->has(Tokenizer\Exception::class));
        self::assertTrue($structures->has(Tokenizer\NamespaceUri::class));
        self::assertTrue($structures->has(Tokenizer\NamespaceUriException::class));
        self::assertTrue($structures->has(Tokenizer\Token::class));
        self::assertTrue($structures->has(Tokenizer\TokenCollection::class));
        self::assertTrue($structures->has(Tokenizer\Tokenizer::class));
        self::assertTrue($structures->has(Tokenizer\XMLSerializer::class));
        self::assertTrue($structures->has(SebastianBergmann\CallableType::class));
        self::assertTrue($structures->has(SebastianBergmann\Exception::class));
        self::assertTrue($structures->has(SebastianBergmann\FalseType::class));
        self::assertTrue($structures->has(SebastianBergmann\GenericObjectType::class));
        self::assertTrue($structures->has(SebastianBergmann\IntersectionType::class));
        self::assertTrue($structures->has(SebastianBergmann\IterableType::class));
        self::assertTrue($structures->has(SebastianBergmann\MixedType::class));
        self::assertTrue($structures->has(SebastianBergmann\NeverType::class));
        self::assertTrue($structures->has(SebastianBergmann\NullType::class));
        self::assertTrue($structures->has(SebastianBergmann\ObjectType::class));
        self::assertTrue($structures->has(SebastianBergmann\ReflectionMapper::class));
        self::assertTrue($structures->has(SebastianBergmann\RuntimeException::class));
        self::assertTrue($structures->has(SebastianBergmann\SimpleType::class));
        self::assertTrue($structures->has(SebastianBergmann\StaticType::class));
        self::assertTrue($structures->has(SebastianBergmann\Type::class));
        self::assertTrue($structures->has(SebastianBergmann\TypeName::class));
        self::assertTrue($structures->has(SebastianBergmann\UnionType::class));
        self::assertTrue($structures->has(SebastianBergmann\UnknownType::class));
        self::assertTrue($structures->has(SebastianBergmann\VoidType::class));
    }

    /**
     * @test
     */
    public function can_inspect_namespaces_using_a_custom_mapper(): void
    {
        $structures = Inspector::getInstance()
                               ->inspect()
                               ->inNamespaces(
                                   'TheSeer\Tokenizer',
                                   'SebastianBergmann\Type'
                               )
                               ->usingMapper(
                                   new ClassmapMapper(require './vendor/composer/autoload_classmap.php')
                               )
                               ->getStructures();

        self::assertCount(27, $structures);
        self::assertTrue($structures->has(Tokenizer\Exception::class));
        self::assertTrue($structures->has(Tokenizer\NamespaceUri::class));
        self::assertTrue($structures->has(Tokenizer\NamespaceUriException::class));
        self::assertTrue($structures->has(Tokenizer\Token::class));
        self::assertTrue($structures->has(Tokenizer\TokenCollection::class));
        self::assertTrue($structures->has(Tokenizer\Tokenizer::class));
        self::assertTrue($structures->has(Tokenizer\XMLSerializer::class));
        self::assertTrue($structures->has(SebastianBergmann\CallableType::class));
        self::assertTrue($structures->has(SebastianBergmann\Exception::class));
        self::assertTrue($structures->has(SebastianBergmann\FalseType::class));
        self::assertTrue($structures->has(SebastianBergmann\GenericObjectType::class));
        self::assertTrue($structures->has(SebastianBergmann\IntersectionType::class));
        self::assertTrue($structures->has(SebastianBergmann\IterableType::class));
        self::assertTrue($structures->has(SebastianBergmann\MixedType::class));
        self::assertTrue($structures->has(SebastianBergmann\NeverType::class));
        self::assertTrue($structures->has(SebastianBergmann\NullType::class));
        self::assertTrue($structures->has(SebastianBergmann\ObjectType::class));
        self::assertTrue($structures->has(SebastianBergmann\ReflectionMapper::class));
        self::assertTrue($structures->has(SebastianBergmann\RuntimeException::class));
        self::assertTrue($structures->has(SebastianBergmann\SimpleType::class));
        self::assertTrue($structures->has(SebastianBergmann\StaticType::class));
        self::assertTrue($structures->has(SebastianBergmann\Type::class));
        self::assertTrue($structures->has(SebastianBergmann\TypeName::class));
        self::assertTrue($structures->has(SebastianBergmann\UnionType::class));
        self::assertTrue($structures->has(SebastianBergmann\UnknownType::class));
        self::assertTrue($structures->has(SebastianBergmann\VoidType::class));
    }

    /**
     * @test
     */
    public function can_inspect_structures_using_a_structure_filter(): void
    {
        $structures = Inspector::getInstance()
                               ->inspect()
                               ->inNamespaces('Smpl\Inspector\Elements')
                               ->where(StructureFilter::make()->uses(HasAttributes::class))
                               ->getStructures();

        self::assertCount(4, $structures);
        self::assertTrue($structures->has(Method::class));
        self::assertTrue($structures->has(Parameter::class));
        self::assertTrue($structures->has(Property::class));
        self::assertTrue($structures->has(Structure::class));
    }

    /**
     * @test
     */
    public function can_retrieve_methods_from_inspected_structures(): void
    {
        $methods = Inspector::getInstance()
                            ->inspect()
                            ->inNamespaces('Smpl\Inspector\Elements')
                            ->where(StructureFilter::make()->uses(HasAttributes::class))
                            ->getMethods();

        self::assertCount(81, $methods);
    }

    /**
     * @test
     */
    public function can_inspect_methods_using_a_method_filter(): void
    {
        $methods = Inspector::getInstance()
                            ->inspect()
                            ->inNamespaces('Smpl\Inspector\Elements')
                            ->where(StructureFilter::make()->uses(HasAttributes::class))
                            ->getMethods(
                                MethodFilter::make()
                                            ->publicOnly()
                                            ->hasReturnType('int')
                            );

        self::assertCount(1, $methods);
        self::assertSame('getPosition', $methods->first()->getName());
        self::assertSame(Parameter::class . Structure::SEPARATOR . 'getPosition', $methods->first()->getFullName());
    }

    /**
     * @test
     */
    public function can_inspect_properties_using_a_property_filter(): void
    {
        $properties = Inspector::getInstance()
                               ->inspect()
                               ->inNamespaces('Smpl\Inspector\Elements')
                               ->where(StructureFilter::make()->uses(HasAttributes::class))
                               ->getProperties(PropertyFilter::make()->hasType(Visibility::class))
                               ->names();

        self::assertCount(2, $properties);
        self::assertContains('Smpl\Inspector\Elements\Method::visibility', $properties);
        self::assertContains('Smpl\Inspector\Elements\Property::visibility', $properties);
    }

    /**
     * @test
     */
    public function throws_an_exception_when_theres_no_source_provided_for_inspection(): void
    {
        $this->expectException(InspectionException::class);
        $this->expectExceptionMessage('No paths, classes or namespaces were provided to inspect');

        Inspector::getInstance()
                 ->inspect()
                 ->getStructures();
    }

    /**
     * @test
     */
    public function wraps_exceptions_in_an_inspection_exception(): void
    {
        $this->expectException(InspectionException::class);
        $this->expectExceptionMessage('Something went wrong during inspection "Provided class \'IDontExist\' is not valid"');

        Inspector::getInstance()->inspectClass('IDontExist');
    }
}