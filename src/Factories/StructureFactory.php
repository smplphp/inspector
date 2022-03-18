<?php

declare(strict_types=1);

namespace Smpl\Inspector\Factories;

use Attribute as BaseAttribute;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;
use Smpl\Inspector\Collections\AttributeMetadata;
use Smpl\Inspector\Collections\MethodAttributes;
use Smpl\Inspector\Collections\MethodParameters;
use Smpl\Inspector\Collections\ParameterAttributes;
use Smpl\Inspector\Collections\PropertyAttributes;
use Smpl\Inspector\Collections\StructureAttributes;
use Smpl\Inspector\Collections\StructureMethods;
use Smpl\Inspector\Collections\StructureProperties;
use Smpl\Inspector\Contracts\Attribute as AttributeContract;
use Smpl\Inspector\Contracts\Metadata as MetadataContract;
use Smpl\Inspector\Contracts\MetadataCollection;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodAttributeCollection;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\Parameter as ParameterContract;
use Smpl\Inspector\Contracts\ParameterAttributeCollection;
use Smpl\Inspector\Contracts\Property as PropertyContract;
use Smpl\Inspector\Contracts\PropertyAttributeCollection;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Contracts\StructureAttributeCollection;
use Smpl\Inspector\Contracts\StructureFactory as StructureFactoryContract;
use Smpl\Inspector\Contracts\StructureMethodCollection;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Smpl\Inspector\Contracts\TypeFactory;
use Smpl\Inspector\Elements\Attribute;
use Smpl\Inspector\Elements\Metadata;
use Smpl\Inspector\Elements\Method;
use Smpl\Inspector\Elements\Parameter;
use Smpl\Inspector\Elements\Property;
use Smpl\Inspector\Elements\Structure;
use Smpl\Inspector\Support\StructureType;

class StructureFactory implements StructureFactoryContract
{
    /**
     * @param class-string $class
     *
     * @return bool
     *
     * @psalm-suppress MixedArgument
     * @psalm-suppress ArgumentTypeCoercion
     */
    public static function isValidClass(string $class): bool
    {
        return class_exists($class)
            || interface_exists($class)
            || enum_exists($class)
            || trait_exists($class);
    }

    private TypeFactory $types;

    /**
     * @var array<class-string, \Smpl\Inspector\Contracts\Structure>
     */
    private array $structures = [];

    /**
     * @var array<class-string, \Smpl\Inspector\Contracts\Attribute>
     */
    private array $attributes = [];

    public function __construct(TypeFactory $types)
    {
        $this->types = $types;
    }

    private function getBaseAttribute(ReflectionAttribute $reflection): BaseAttribute
    {
        if (! self::isValidClass($reflection->getName())) {
            throw new InvalidArgumentException(sprintf(
                'Invalid attribute provided \'%s\'', $reflection->getName()
            ));
        }

        $classReflection = new ReflectionClass($reflection->getName());
        $baseAttribute   = $classReflection->getAttributes(
                BaseAttribute::class, ReflectionAttribute::IS_INSTANCEOF
            )[0] ?? null;

        if ($baseAttribute === null) {
            throw new InvalidArgumentException(sprintf(
                'Invalid attribute provided \'%s\'', $reflection->getName()
            ));
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $baseAttribute->newInstance();
    }

    /**
     * @param \ReflectionMethod|string|\Smpl\Inspector\Contracts\Method              $method
     * @param \ReflectionClass|class-string|\Smpl\Inspector\Contracts\Structure|null $class
     *
     * @return array{MethodContract, StructureContract}
     *
     * @throws \ReflectionException
     * @psalm-suppress PossiblyInvalidMethodCall
     */
    private function getMethodAndStructure(ReflectionMethod|string|MethodContract $method, ReflectionClass|string|StructureContract|null $class): array
    {
        if ($method instanceof MethodContract) {
            $structure = $method->getStructure();
        } else {
            if (is_string($method) && $class === null) {
                throw new InvalidArgumentException(
                    'No class/structure provided for method when attempting to retrieve parameters'
                );
            }

            $structure = $class instanceof StructureContract ? $class : $this->makeStructure(
                $class ?? $method->getDeclaringClass()
            );
            $method    = $this->makeMethod($method, $structure);
        }

        return [$method, $structure];
    }

    private function getStructureType(ReflectionClass $reflection): StructureType
    {
        if ($reflection->isEnum()) {
            return StructureType::Enum;
        }

        if ($reflection->isInterface()) {
            return StructureType::Interface;
        }

        if ($reflection->isTrait()) {
            return StructureType::Trait;
        }

        if (! empty($reflection->getAttributes(BaseAttribute::class))) {
            return StructureType::Attribute;
        }

        return StructureType::Default;
    }

    private function getStructure(string $name): ?StructureContract
    {
        return $this->structures[$name] ?? null;
    }

    private function hasStructure(string $name): bool
    {
        return $this->getStructure($name) !== null;
    }

    private function addStructure(Structure $structure): static
    {
        $this->structures[$structure->getFullName()] = $structure;
        return $this;
    }

    /**
     * @param \ReflectionClass|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Structure
     *
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function makeStructure(ReflectionClass|string $class): StructureContract
    {
        $className = is_string($class) ? $class : $class->getName();

        if ($this->hasStructure($className)) {
            return $this->getStructure($className);
        }

        if ($class instanceof ReflectionClass) {
            return $this->makeFromReflection($class);
        }

        if (self::isValidClass($class)) {
            try {
                return $this->makeFromReflection(new ReflectionClass($class));
                // @codeCoverageIgnoreStart
            } catch (ReflectionException) {
            }
            // @codeCoverageIgnoreEnd
        }

        throw new RuntimeException(sprintf('Provided class \'%s\' is invalid', $class));
    }

    /**
     * @param ReflectionAttribute[] $attributesReflections
     *
     * @return array{list<AttributeContract>, array<class-string, MetadataCollection>}
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    private function makeAttributesAndMetadata(array $attributesReflections): array
    {
        $attributes = $metadata = [];

        foreach ($attributesReflections as $attributesReflection) {
            $attribute = $this->makeAttribute($attributesReflection);

            if (! isset($attributes[$attribute->getName()])) {
                $attributes[$attribute->getName()] = $attribute;
            }

            $metadata[$attribute->getName()][] = $this->makeMetadata($attribute, $attributesReflection);
        }

        /**
         * @var array<class-string, MetadataCollection> $attributeMetadata
         */
        $attributeMetadata = [];

        foreach ($metadata as $attributeName => $metadataArray) {
            $attribute                                = $attributes[$attributeName];
            $attributeMetadata[$attribute->getName()] = new AttributeMetadata(
                $attribute,
                $metadataArray
            );
        }

        return [array_values($attributes), $attributeMetadata];
    }

    /**
     * @param \ReflectionClass $reflection
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    private function makeFromReflection(ReflectionClass $reflection): StructureContract
    {
        $structure = new Structure(
            $reflection,
            $this->getStructureType($reflection),
            $this->types->make($reflection->getName())
        );

        $this->addStructure($structure);

        return $structure;
    }

    /**
     * @param string|\ReflectionProperty                                        $property
     * @param \ReflectionClass|class-string|\Smpl\Inspector\Contracts\Structure $class
     *
     * @return \Smpl\Inspector\Contracts\Property
     * @throws \ReflectionException
     */
    public function makeProperty(string|ReflectionProperty $property, ReflectionClass|string|StructureContract $class): PropertyContract
    {
        $structure = $class instanceof StructureContract ? $class : $this->makeStructure($class);

        if (! $structure->getStructureType()->canHaveProperties()) {
            throw new RuntimeException(sprintf(
                'Structures of type \'%s\' do not have properties',
                $structure->getStructureType()->value
            ));
        }

        $reflection = $property instanceof ReflectionProperty
            ? $property
            : $structure->getReflection()->getProperty($property);

        return new Property(
            $structure,
            $reflection,
            $reflection->hasType()
                ? $this->types->make($reflection->getType())
                : null
        );
    }

    /**
     * @param \ReflectionClass|class-string|\Smpl\Inspector\Contracts\Structure $class
     *
     * @return \Smpl\Inspector\Contracts\StructurePropertyCollection
     * @throws \ReflectionException
     */
    public function makeProperties(ReflectionClass|string|StructureContract $class): StructurePropertyCollection
    {
        $structure           = $class instanceof StructureContract ? $class : $this->makeStructure($class);
        $propertyReflections = $structure->getReflection()->getProperties();
        $properties          = [];

        foreach ($propertyReflections as $propertyReflection) {
            $properties[$propertyReflection->getName()] = $this->makeProperty($propertyReflection, $structure);
        }

        return new StructureProperties($structure, $properties);
    }

    /**
     * @param \ReflectionMethod|string                                          $method
     * @param \ReflectionClass|class-string|\Smpl\Inspector\Contracts\Structure $class
     *
     * @return \Smpl\Inspector\Contracts\Method
     * @throws \ReflectionException
     *
     * @psalm-suppress PossiblyNullArgument
     */
    public function makeMethod(ReflectionMethod|string $method, ReflectionClass|string|StructureContract $class): MethodContract
    {
        $structure = $class instanceof StructureContract ? $class : $this->makeStructure($class);

        $reflection = $method instanceof ReflectionMethod
            ? $method
            : $structure->getReflection()->getMethod($method);

        return new Method(
            $structure,
            $reflection,
            $reflection->hasReturnType()
                ? $this->types->make($reflection->getReturnType())
                : null
        );
    }

    /**
     * @param \ReflectionClass|class-string|\Smpl\Inspector\Contracts\Structure $class
     *
     * @return \Smpl\Inspector\Contracts\StructureMethodCollection
     * @throws \ReflectionException
     */
    public function makeMethods(ReflectionClass|string|StructureContract $class): StructureMethodCollection
    {
        $structure         = $class instanceof StructureContract ? $class : $this->makeStructure($class);
        $methodReflections = $structure->getReflection()->getMethods();
        $methods           = [];

        foreach ($methodReflections as $methodReflection) {
            $methods[$methodReflection->getShortName()] = $this->makeMethod($methodReflection, $structure);
        }

        return new StructureMethods($structure, $methods);
    }

    public function makeParameter(ReflectionParameter $parameter, ReflectionMethod|string|MethodContract $method, ReflectionClass|string|StructureContract $class): ParameterContract
    {
        [$method,] = $this->getMethodAndStructure($method, $class);

        return new Parameter(
            $method,
            $parameter,
            $parameter->hasType()
                ? $this->types->make($parameter->getType())
                : null
        );
    }

    public function makeParameters(ReflectionMethod|string|MethodContract $method, ReflectionClass|string|StructureContract|null $class = null): MethodParameterCollection
    {
        [$method, $structure] = $this->getMethodAndStructure($method, $class);
        $parameterReflections = $method->getReflection()->getParameters();
        $parameters           = [];

        foreach ($parameterReflections as $parameterReflection) {
            $parameters[] = $this->makeParameter($parameterReflection, $method, $structure);
        }

        return new MethodParameters($method, $parameters);
    }

    /**
     * @param \ReflectionAttribute $reflection
     *
     * @return \Smpl\Inspector\Contracts\Attribute
     *
     * @psalm-suppress PropertyTypeCoercion
     */
    public function makeAttribute(ReflectionAttribute $reflection): AttributeContract
    {
        if (! isset($this->attributes[$reflection->getName()])) {
            $this->attributes[$reflection->getName()] = new Attribute(
                $reflection->getName(),
                $this->getBaseAttribute($reflection)
            );
        }

        return $this->attributes[$reflection->getName()];
    }

    public function makeStructureAttributes(StructureContract $structure): StructureAttributeCollection
    {
        $reflection            = $structure->getReflection();
        $attributesReflections = $reflection->getAttributes();

        if (empty($attributesReflections)) {
            return new StructureAttributes($structure, [], []);
        }

        [$attributes, $metadata] = $this->makeAttributesAndMetadata($attributesReflections);

        /**
         * @psalm-suppress InvalidArgument
         */
        return new StructureAttributes($structure, $attributes, $metadata);
    }

    public function makePropertyAttributes(PropertyContract $property): PropertyAttributeCollection
    {
        $reflection            = $property->getReflection();
        $attributesReflections = $reflection->getAttributes();

        if (empty($attributesReflections)) {
            return new PropertyAttributes($property, [], []);
        }

        [$attributes, $metadata] = $this->makeAttributesAndMetadata($attributesReflections);

        /**
         * @psalm-suppress InvalidArgument
         */
        return new PropertyAttributes($property, $attributes, $metadata);
    }

    public function makeMethodAttributes(MethodContract $method): MethodAttributeCollection
    {
        $reflection            = $method->getReflection();
        $attributesReflections = $reflection->getAttributes();

        if (empty($attributesReflections)) {
            return new MethodAttributes($method, [], []);
        }

        [$attributes, $metadata] = $this->makeAttributesAndMetadata($attributesReflections);

        /**
         * @psalm-suppress InvalidArgument
         */
        return new MethodAttributes($method, $attributes, $metadata);
    }

    public function makeParameterAttributes(ParameterContract $parameter): ParameterAttributeCollection
    {
        $reflection            = $parameter->getReflection();
        $attributesReflections = $reflection->getAttributes();

        if (empty($attributesReflections)) {
            return new ParameterAttributes($parameter, [], []);
        }

        [$attributes, $metadata] = $this->makeAttributesAndMetadata($attributesReflections);

        /**
         * @psalm-suppress InvalidArgument
         */
        return new ParameterAttributes($parameter, $attributes, $metadata);
    }

    /**
     * @param \Smpl\Inspector\Contracts\Attribute $attribute
     * @param \ReflectionAttribute                $reflection
     *
     * @return \Smpl\Inspector\Contracts\Metadata
     */
    public function makeMetadata(AttributeContract $attribute, ReflectionAttribute $reflection): MetadataContract
    {
        return new Metadata(
            $attribute,
            $reflection
        );
    }
}