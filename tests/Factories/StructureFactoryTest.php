<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Factories;

use Attribute;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionFunction;
use Smpl\Inspector\Exceptions\AttributeException;
use Smpl\Inspector\Exceptions\StructureException;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\StructureType;
use Smpl\Inspector\Tests\Fixtures\BasicInterface;
use Smpl\Inspector\Tests\Fixtures\BasicTrait;
use Smpl\Inspector\Tests\Fixtures\ClassAttribute;
use Smpl\Inspector\Tests\Fixtures\ExampleClass;
use Smpl\Inspector\Tests\Fixtures\ExampleEnum;
use Smpl\Inspector\Tests\Fixtures\InvalidAttributeClass;
use Smpl\Inspector\Tests\Fixtures\MethodAttribute;
use Smpl\Inspector\Tests\Fixtures\ParameterAttribute;
use Smpl\Inspector\Tests\Fixtures\PropertyAttribute;
use Smpl\Inspector\Tests\Fixtures\SecondClassAttribute;
use Smpl\Inspector\Types\IntType;

/**
 * @group types
 * @group factories
 */
class StructureFactoryTest extends TestCase
{
    private StructureFactory $structureFactory;

    protected function setUp(): void
    {
        $this->structureFactory = Inspector::getInstance()->structures();
    }

    /**
     * @test
     */
    public function creates_structure_from_class_string(): void
    {
        $structure = $this->structureFactory->makeStructure(ExampleClass::class);

        self::assertSame(ExampleClass::class, $structure->getFullName());
        self::assertSame(StructureType::Default, $structure->getStructureType());
    }

    /**
     * @test
     */
    public function creates_structure_from_interface_string(): void
    {
        $structure = $this->structureFactory->makeStructure(BasicInterface::class);

        self::assertSame(BasicInterface::class, $structure->getFullName());
        self::assertSame(StructureType::Interface, $structure->getStructureType());
    }

    /**
     * @test
     */
    public function creates_structure_from_trait_string(): void
    {
        $structure = $this->structureFactory->makeStructure(BasicTrait::class);

        self::assertSame(BasicTrait::class, $structure->getFullName());
        self::assertSame(StructureType::Trait, $structure->getStructureType());
    }

    /**
     * @test
     */
    public function creates_structure_from_attribute_string(): void
    {
        $structure = $this->structureFactory->makeStructure(ClassAttribute::class);

        self::assertSame(ClassAttribute::class, $structure->getFullName());
        self::assertSame(StructureType::Attribute, $structure->getStructureType());
    }

    /**
     * @test
     */
    public function creates_structure_from_enum_string(): void
    {
        $structure = $this->structureFactory->makeStructure(ExampleEnum::class);

        self::assertSame(ExampleEnum::class, $structure->getFullName());
        self::assertSame(StructureType::Enum, $structure->getStructureType());
    }

    /**
     * @test
     */
    public function throws_an_exception_when_trying_to_create_a_structure_for_an_invalid_class(): void
    {
        $this->expectException(StructureException::class);
        $this->expectExceptionMessage('Provided class \'InvalidClass\' is not valid');

        $this->structureFactory->makeStructure('InvalidClass');
    }

    /**
     * @test
     */
    public function creates_only_one_instance_of_a_structure(): void
    {
        $structure1 = $this->structureFactory->makeStructure(ExampleClass::class);
        $structure2 = $this->structureFactory->makeStructure(ExampleClass::class);

        self::assertSame($structure1, $structure2);
    }

    /**
     * @test
     */
    public function creates_structure_collection_from_class_strings(): void
    {
        $structures = $this->structureFactory->makeStructures(
            ExampleClass::class, BasicInterface::class
        );

        self::assertCount(2, $structures);
        self::assertTrue($structures->has(ExampleClass::class));
        self::assertTrue($structures->has(BasicInterface::class));
    }

    /**
     * @test
     */
    public function creates_structure_collection_from_reflections(): void
    {
        $structures = $this->structureFactory->makeStructures(
            new ReflectionClass(ExampleClass::class),
            new ReflectionClass(BasicInterface::class)
        );

        self::assertCount(2, $structures);
        self::assertTrue($structures->has(ExampleClass::class));
        self::assertTrue($structures->has(BasicInterface::class));
    }

    /**
     * @test
     */
    public function creates_property_from_reflection(): void
    {
        $structure = $this->structureFactory->makeStructure(ExampleClass::class);
        $property  = $this->structureFactory->makeProperty($structure->getReflection()->getProperty('publicStringProperty'));

        self::assertSame('publicStringProperty', $property->getName());
        self::assertSame(ExampleClass::class . '::publicStringProperty', $property->getFullName());
    }

    /**
     * @test
     */
    public function creates_only_one_instance_of_a_property(): void
    {
        $structure = $this->structureFactory->makeStructure(ExampleClass::class);
        $property1 = $this->structureFactory->makeProperty($structure->getReflection()->getProperty('publicStringProperty'));
        $property2 = $this->structureFactory->makeProperty($structure->getReflection()->getProperty('publicStringProperty'));

        self::assertSame($property1, $property2);
    }

    /**
     * @test
     */
    public function creates_property_collection_from_reflections(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $collection = $this->structureFactory->makeProperties(...$structure->getReflection()->getProperties());

        self::assertCount(9, $collection);
    }

    /**
     * @test
     */
    public function creates_property_collection_from_structure(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $collection = $this->structureFactory->makeStructureProperties($structure);

        self::assertCount(9, $collection);
        self::assertSame($structure, $collection->getStructure());
    }

    /**
     * @test
     */
    public function throws_an_exception_when_trying_to_get_properties_from_a_structure_that_cant_have_properties(): void
    {
        $this->expectException(StructureException::class);
        $this->expectExceptionMessage('Class \'' . BasicInterface::class . '\' of type \'Interface\' does not support properties');

        $this->structureFactory->makeStructureProperties(
            $this->structureFactory->makeStructure(BasicInterface::class)
        );
    }

    /**
     * @test
     */
    public function creates_method_from_reflection(): void
    {
        $structure = $this->structureFactory->makeStructure(ExampleClass::class);
        $method    = $this->structureFactory->makeMethod($structure->getReflection()->getMethod('attributedPublicMethodWithoutParameters'));

        self::assertSame('attributedPublicMethodWithoutParameters', $method->getName());
        self::assertSame(ExampleClass::class . '::attributedPublicMethodWithoutParameters', $method->getFullName());
    }

    /**
     * @test
     */
    public function creates_only_one_instance_of_a_method(): void
    {
        $structure = $this->structureFactory->makeStructure(ExampleClass::class);
        $method1   = $this->structureFactory->makeMethod($structure->getReflection()->getMethod('attributedPublicMethodWithoutParameters'));
        $method2   = $this->structureFactory->makeMethod($structure->getReflection()->getMethod('attributedPublicMethodWithoutParameters'));

        self::assertSame($method1, $method2);
    }

    /**
     * @test
     */
    public function creates_method_collection_from_reflections(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $collection = $this->structureFactory->makeMethods(...$structure->getReflection()->getMethods());

        self::assertCount(7, $collection);
    }

    /**
     * @test
     */
    public function creates_method_collection_from_structure(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $collection = $this->structureFactory->makeStructureMethods($structure);

        self::assertCount(7, $collection);
        self::assertSame($structure, $collection->getStructure());
    }

    /**
     * @test
     */
    public function creates_parameter_from_reflection(): void
    {
        $structure   = $this->structureFactory->makeStructure(ExampleClass::class);
        $method      = $this->structureFactory->makeMethod($structure->getReflection()->getMethod('protectedMethodWithAParameter'));
        $reflections = $method->getReflection()->getParameters();
        $parameter   = $this->structureFactory->makeParameter($reflections[0]);

        self::assertSame('number', $parameter->getName());
        self::assertInstanceOf(IntType::class, $parameter->getType());
    }

    /**
     * @test
     */
    public function creates_parameter_collection_from_reflections(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $method     = $this->structureFactory->makeMethod($structure->getReflection()->getMethod('protectedMethodWithAParameter'));
        $collection = $this->structureFactory->makeParameters(...$method->getReflection()->getParameters());

        self::assertCount(1, $collection);
    }

    /**
     * @test
     */
    public function creates_parameter_collection_from_method(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $method     = $this->structureFactory->makeMethod($structure->getReflection()->getMethod('protectedMethodWithAParameter'));
        $collection = $this->structureFactory->makeMethodParameters($method);

        self::assertCount(1, $collection);
        self::assertSame($method, $collection->getMethod());
    }

    /**
     * @test
     */
    public function throws_an_exception_when_trying_to_create_a_parameter_for_a_function_not_method(): void
    {
        $reflection = new ReflectionFunction('array_map');

        $this->expectException(StructureException::class);
        $this->expectExceptionMessage('Functions are not currently supported');

        $this->structureFactory->makeParameter($reflection->getParameters()[0]);
    }

    /**
     * @test
     */
    public function creates_attribute_from_class_string(): void
    {
        $attribute = $this->structureFactory->makeAttribute(ClassAttribute::class);

        self::assertSame(ClassAttribute::class, $attribute->getName());
    }

    /**
     * @test
     */
    public function throws_an_exception_when_trying_to_create_an_attribute_for_phps_core_attribute_class(): void
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('Cannot create an attribute instance for PHPs base attribute');

        $this->structureFactory->makeAttribute(Attribute::class);
    }

    /**
     * @test
     */
    public function throws_an_exception_when_trying_to_create_an_attribute_for_a_non_attribute(): void
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('Attribute \'Smpl\Inspector\Tests\Fixtures\BasicInterface\' is not a valid attribute');

        $this->structureFactory->makeAttribute(BasicInterface::class);
    }

    /**
     * @test
     */
    public function throws_an_exception_when_trying_to_create_an_attribute_for_an_invalid_class(): void
    {
        $this->expectException(StructureException::class);
        $this->expectExceptionMessage('Provided class \'invalid\' is not valid');

        $this->structureFactory->makeAttribute('invalid');
    }

    /**
     * @test
     */
    public function creates_only_one_instance_of_an_attribute(): void
    {
        self::assertSame(
            $this->structureFactory->makeAttribute(ClassAttribute::class),
            $this->structureFactory->makeAttribute(ClassAttribute::class)
        );
    }

    /**
     * @test
     */
    public function creates_metadata_from_reflection(): void
    {
        $structure = $this->structureFactory->makeStructure(ExampleClass::class);
        $metadata  = $this->structureFactory->makeMetadatum(
            $structure->getReflection()->getAttributes(ClassAttribute::class)[0]
        );

        self::assertSame(ClassAttribute::class, $metadata->getAttribute()->getName());
    }

    /**
     * @test
     */
    public function creates_metadata_collection_from_reflections(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $collection = $this->structureFactory->makeMetadata(...$structure->getReflection()->getAttributes());

        self::assertCount(3, $collection);
    }

    /**
     * @test
     */
    public function creates_metadata_collection_from_structure(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $collection = $this->structureFactory->makeStructureMetadata($structure);

        self::assertCount(3, $collection);
        self::assertSame($structure, $collection->getStructure());
        self::assertCount(2, $collection->getAttributes());
    }

    /**
     * @test
     */
    public function skips_phps_core_attribute_when_creating_metadata_collection(): void
    {
        $structure  = $this->structureFactory->makeStructure(MethodAttribute::class);
        $collection = $this->structureFactory->makeStructureMetadata($structure);

        self::assertCount(1, $collection);
        self::assertFalse($collection->has(Attribute::class));
    }

    /**
     * @test
     */
    public function creates_metadata_collection_from_property(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $property   = $structure->getProperty('publicStringProperty');
        $collection = $this->structureFactory->makePropertyMetadata($property);

        self::assertCount(1, $collection);
        self::assertSame($property, $collection->getProperty());
    }

    /**
     * @test
     */
    public function creates_metadata_collection_from_method(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $method     = $structure->getMethod('attributedPublicMethodWithoutParameters');
        $collection = $this->structureFactory->makeMethodMetadata($method);

        self::assertCount(1, $collection);
        self::assertSame($method, $collection->getMethod());
    }

    /**
     * @test
     */
    public function creates_metadata_collection_from_parameter(): void
    {
        $structure  = $this->structureFactory->makeStructure(ExampleClass::class);
        $parameter  = $structure->getConstructor()->getParameter('someNumber');
        $collection = $this->structureFactory->makeParameterMetadata($parameter);

        self::assertCount(1, $collection);
        self::assertSame($parameter, $collection->getParameter());
    }

    /**
     * @test
     */
    public function throws_an_exception_when_a_non_repeatable_class_attribute_is_repeated(): void
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('Attribute \'' . SecondClassAttribute::class . '\' is not repeatable, but is provided multiple times');

        $structure = $this->structureFactory->makeStructure(InvalidAttributeClass::class);
        $this->structureFactory->makeStructureMetadata($structure);
    }

    /**
     * @test
     */
    public function throws_an_exception_when_a_non_repeatable_property_attribute_is_repeated(): void
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('Attribute \'' . PropertyAttribute::class . '\' is not repeatable, but is provided multiple times');

        $structure = $this->structureFactory->makeStructure(InvalidAttributeClass::class);
        $this->structureFactory->makePropertyMetadata($structure->getProperty('invalidAttributeProperty'));
    }

    /**
     * @test
     */
    public function throws_an_exception_when_a_non_repeatable_method_attribute_is_repeated(): void
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('Attribute \'' . MethodAttribute::class . '\' is not repeatable, but is provided multiple times');

        $structure = $this->structureFactory->makeStructure(InvalidAttributeClass::class);
        $this->structureFactory->makeMethodMetadata($structure->getMethod('invalidAttributeMethod'));
    }

    /**
     * @test
     */
    public function throws_an_exception_when_a_non_repeatable_parameter_attribute_is_repeated(): void
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('Attribute \'' . ParameterAttribute::class . '\' is not repeatable, but is provided multiple times');

        $structure = $this->structureFactory->makeStructure(InvalidAttributeClass::class);
        $method    = $structure->getMethod('anotherMethod');
        $this->structureFactory->makeParameterMetadata($method->getParameter('uhoh'));
    }

    /**
     * @test
     */
    public function throws_an_exception_when_an_attribute_does_not_have_the_correct_target(): void
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('Attribute \'' . MethodAttribute::class . '\' is not valid for the target \'Property\'');

        $structure = $this->structureFactory->makeStructure(InvalidAttributeClass::class);
        $this->structureFactory->makePropertyMetadata($structure->getProperty('anotherInvalidAttributeProperty'));
    }
}