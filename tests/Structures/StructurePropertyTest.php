<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Structures;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Smpl\Inspector\Exceptions\StructureException;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Filters\PropertyFilter;
use Smpl\Inspector\Support\Visibility;
use Smpl\Inspector\Tests\Fixtures\ExampleInterface;
use Smpl\Inspector\Tests\Fixtures\TypeReflectableClass;
use Smpl\Inspector\Types\ArrayType;
use Smpl\Inspector\Types\BoolType;
use Smpl\Inspector\Types\ClassType;
use Smpl\Inspector\Types\FloatType;
use Smpl\Inspector\Types\IntersectionType;
use Smpl\Inspector\Types\IntType;
use Smpl\Inspector\Types\IterableType;
use Smpl\Inspector\Types\MixedType;
use Smpl\Inspector\Types\NullableType;
use Smpl\Inspector\Types\ObjectType;
use Smpl\Inspector\Types\StringType;
use Smpl\Inspector\Types\UnionType;
use Traversable;

/**
 * @group factories
 * @group structures
 */
class StructurePropertyTest extends TestCase
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
    public function correctly_loads_properties_for_classes(): void
    {
        $structure  = $this->factory->makeStructure(TypeReflectableClass::class);
        $properties = $structure->getProperties();

        self::assertCount(13, $properties);
    }

    /**
     * @test
     */
    public function structure_properties_are_accurate(): void
    {
        $structure  = $this->factory->makeStructure(TypeReflectableClass::class);
        $properties = $structure->getProperties();

        self::assertTrue($properties->has('array'));
        self::assertTrue($properties->has('bool'));
        self::assertTrue($properties->has('class'));
        self::assertTrue($properties->has('float'));
        self::assertTrue($properties->has('intersection'));
        self::assertTrue($properties->has('int'));
        self::assertTrue($properties->has('iterable'));
        self::assertTrue($properties->has('mixed'));
        self::assertTrue($properties->has('nullable'));
        self::assertTrue($properties->has('object'));
        self::assertTrue($properties->has('string'));
        self::assertTrue($properties->has('union'));
        self::assertTrue($properties->has('noType'));

        self::assertSame($structure, $properties->get('array')->getStructure());
        self::assertSame($structure, $properties->get('bool')->getStructure());
        self::assertSame($structure, $properties->get('class')->getStructure());
        self::assertSame($structure, $properties->get('float')->getStructure());
        self::assertSame($structure, $properties->get('intersection')->getStructure());
        self::assertSame($structure, $properties->get('int')->getStructure());
        self::assertSame($structure, $properties->get('iterable')->getStructure());
        self::assertSame($structure, $properties->get('mixed')->getStructure());
        self::assertSame($structure, $properties->get('nullable')->getStructure());
        self::assertSame($structure, $properties->get('object')->getStructure());
        self::assertSame($structure, $properties->get('string')->getStructure());
        self::assertSame($structure, $properties->get('union')->getStructure());
        self::assertSame($structure, $properties->get('noType')->getStructure());

        self::assertEquals('array', $properties->get('array')->getName());
        self::assertEquals('bool', $properties->get('bool')->getName());
        self::assertEquals('class', $properties->get('class')->getName());
        self::assertEquals('float', $properties->get('float')->getName());
        self::assertEquals('intersection', $properties->get('intersection')->getName());
        self::assertEquals('int', $properties->get('int')->getName());
        self::assertEquals('iterable', $properties->get('iterable')->getName());
        self::assertEquals('mixed', $properties->get('mixed')->getName());
        self::assertEquals('nullable', $properties->get('nullable')->getName());
        self::assertEquals('object', $properties->get('object')->getName());
        self::assertEquals('string', $properties->get('string')->getName());
        self::assertEquals('union', $properties->get('union')->getName());
        self::assertEquals('noType', $properties->get('noType')->getName());

        self::assertFalse($properties->get('array')->isStatic());
        self::assertFalse($properties->get('bool')->isStatic());
        self::assertFalse($properties->get('class')->isStatic());
        self::assertFalse($properties->get('float')->isStatic());
        self::assertFalse($properties->get('intersection')->isStatic());
        self::assertFalse($properties->get('int')->isStatic());
        self::assertTrue($properties->get('iterable')->isStatic());
        self::assertFalse($properties->get('mixed')->isStatic());
        self::assertFalse($properties->get('nullable')->isStatic());
        self::assertFalse($properties->get('object')->isStatic());
        self::assertFalse($properties->get('string')->isStatic());
        self::assertFalse($properties->get('union')->isStatic());
        self::assertFalse($properties->get('noType')->isStatic());

        self::assertTrue($properties->get('array')->hasDefault());
        self::assertFalse($properties->get('bool')->hasDefault());
        self::assertFalse($properties->get('class')->hasDefault());
        self::assertFalse($properties->get('float')->hasDefault());
        self::assertFalse($properties->get('intersection')->hasDefault());
        self::assertFalse($properties->get('int')->hasDefault());
        self::assertFalse($properties->get('iterable')->hasDefault());
        self::assertFalse($properties->get('mixed')->hasDefault());
        self::assertFalse($properties->get('nullable')->hasDefault());
        self::assertFalse($properties->get('object')->hasDefault());
        self::assertFalse($properties->get('string')->hasDefault());
        self::assertFalse($properties->get('union')->hasDefault());
        self::assertTrue($properties->get('noType')->hasDefault());

        self::assertEquals([], $properties->get('array')->getDefault());
        self::assertEquals(null, $properties->get('noType')->getDefault());

        self::assertFalse($properties->get('array')->isNullable());
        self::assertFalse($properties->get('bool')->isNullable());
        self::assertFalse($properties->get('class')->isNullable());
        self::assertFalse($properties->get('float')->isNullable());
        self::assertFalse($properties->get('intersection')->isNullable());
        self::assertFalse($properties->get('int')->isNullable());
        self::assertFalse($properties->get('iterable')->isNullable());
        self::assertTrue($properties->get('mixed')->isNullable());
        self::assertTrue($properties->get('nullable')->isNullable());
        self::assertFalse($properties->get('object')->isNullable());
        self::assertFalse($properties->get('string')->isNullable());
        self::assertFalse($properties->get('union')->isNullable());
        self::assertTrue($properties->get('noType')->isNullable());

        self::assertSame(Visibility::Public, $properties->get('array')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('bool')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('class')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('float')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('intersection')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('int')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('iterable')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('mixed')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('nullable')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('object')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('string')->getVisibility());
        self::assertSame(Visibility::Public, $properties->get('union')->getVisibility());
        self::assertSame(Visibility::Private, $properties->get('noType')->getVisibility());

        self::assertInstanceOf(ArrayType::class, $properties->get('array')->getType());
        self::assertInstanceOf(BoolType::class, $properties->get('bool')->getType());
        self::assertInstanceOf(ClassType::class, $properties->get('class')->getType());
        self::assertInstanceOf(FloatType::class, $properties->get('float')->getType());
        self::assertInstanceOf(IntersectionType::class, $properties->get('intersection')->getType());
        self::assertInstanceOf(IntType::class, $properties->get('int')->getType());
        self::assertInstanceOf(IterableType::class, $properties->get('iterable')->getType());
        self::assertInstanceOf(MixedType::class, $properties->get('mixed')->getType());
        self::assertInstanceOf(NullableType::class, $properties->get('nullable')->getType());
        self::assertInstanceOf(ObjectType::class, $properties->get('object')->getType());
        self::assertInstanceOf(StringType::class, $properties->get('string')->getType());
        self::assertInstanceOf(UnionType::class, $properties->get('union')->getType());
        self::assertNull($properties->get('noType')->getType());
    }

    /**
     * @test
     */
    public function structure_property_collections_are_iterable(): void
    {
        $structure  = $this->factory->makeStructure(TypeReflectableClass::class);
        $properties = $structure->getProperties();

        self::assertIsIterable($properties);
        self::assertInstanceOf(Traversable::class, $properties->getIterator());
    }

    /**
     * @test
     */
    public function structure_property_collections_belong_to_their_parent_structure(): void
    {
        $structure  = $this->factory->makeStructure(TypeReflectableClass::class);
        $properties = $structure->getProperties();

        self::assertSame($structure, $properties->getStructure());
    }

    /**
     * @test
     */
    public function can_filter_properties_by_their_static_modifier(): void
    {
        $structure           = $this->factory->makeStructure(TypeReflectableClass::class);
        $staticProperties    = $structure->getProperties()->filter(PropertyFilter::make()->static());
        $nonStaticProperties = $structure->getProperties()->filter(PropertyFilter::make()->notStatic());

        self::assertCount(1, $staticProperties);
        self::assertCount(12, $nonStaticProperties);
    }

    /**
     * @test
     */
    public function can_filter_properties_by_their_visibility(): void
    {
        $structure           = $this->factory->makeStructure(TypeReflectableClass::class);
        $publicProperties    = $structure->getProperties()->filter(PropertyFilter::make()->publicOnly());
        $protectedProperties = $structure->getProperties()->filter(PropertyFilter::make()->protectedOnly());
        $privateProperties   = $structure->getProperties()->filter(PropertyFilter::make()->privateOnly());

        self::assertCount(12, $publicProperties);
        self::assertCount(0, $protectedProperties);
        self::assertCount(1, $privateProperties);

        $publicProperties2    = $structure->getProperties()->filter(
            PropertyFilter::make()->hasVisibility(Visibility::Public)
        );
        $protectedProperties2 = $structure->getProperties()->filter(
            PropertyFilter::make()->hasVisibility(Visibility::Protected)
        );
        $privateProperties2   = $structure->getProperties()->filter(
            PropertyFilter::make()->hasVisibility(Visibility::Private)
        );

        self::assertCount(12, $publicProperties2);
        self::assertCount(0, $protectedProperties2);
        self::assertCount(1, $privateProperties2);
    }

    /**
     * @test
     */
    public function can_filter_properties_by_their_nullability(): void
    {
        $structure             = $this->factory->makeStructure(TypeReflectableClass::class);
        $nullableProperties    = $structure->getProperties()->filter(PropertyFilter::make()->nullable());
        $nonNullableProperties = $structure->getProperties()->filter(PropertyFilter::make()->notNullable());

        self::assertCount(3, $nullableProperties);
        self::assertCount(10, $nonNullableProperties);
    }

    /**
     * @test
     */
    public function can_filter_properties_by_having_a_default_value(): void
    {
        $structure            = $this->factory->makeStructure(TypeReflectableClass::class);
        $defaultProperties    = $structure->getProperties()->filter(PropertyFilter::make()->hasDefaultValue());
        $nonDefaultProperties = $structure->getProperties()->filter(PropertyFilter::make()->noDefaultValue());

        self::assertCount(2, $defaultProperties);
        self::assertCount(11, $nonDefaultProperties);
    }

    /**
     * @test
     */
    public function can_filter_properties_by_whether_they_have_a_type(): void
    {
        $structure          = $this->factory->makeStructure(TypeReflectableClass::class);
        $typedProperties    = $structure->getProperties()->filter(PropertyFilter::make()->typed());
        $notTypedProperties = $structure->getProperties()->filter(PropertyFilter::make()->notTyped());

        self::assertCount(12, $typedProperties);
        self::assertCount(1, $notTypedProperties);
    }

    /**
     * @test
     */
    public function can_filter_properties_by_their_type(): void
    {
        $structure        = $this->factory->makeStructure(TypeReflectableClass::class);
        $stringProperties = $structure->getProperties()->filter(PropertyFilter::make()->hasType('string'));
        $typeProperties   = $structure->getProperties()->filter(
            PropertyFilter::make()->hasType($this->types->make('string'))
        );

        self::assertCount(3, $stringProperties);
        self::assertCount(3, $typeProperties);
    }

    /**
     * @test
     */
    public function can_get_property_from_structure_reflection(): void
    {
        $structure = $this->factory->makeStructure(TypeReflectableClass::class);
        $property  = $this->factory->makeProperty('array', $structure);

        self::assertNotNull($property);
    }

    /**
     * @test
     */
    public function throws_an_exception_when_the_structure_cant_have_properties(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Structures of type \'interface\' do not have properties');

        $this->factory->makeStructure(ExampleInterface::class)->getProperties();
    }

    /**
     * @test
     */
    public function throws_an_exception_when_the_structure_cant_have_properties_and_accessing_properties_directly(): void
    {
        $this->expectException(StructureException::class);
        $this->expectExceptionMessage('Class \'' . ExampleInterface::class . '\' of type \'interface\' does not support properties');

        $this->factory->makeProperty('invalid', ExampleInterface::class);
    }
}