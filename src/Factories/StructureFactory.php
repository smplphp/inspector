<?php

declare(strict_types=1);

namespace Smpl\Inspector\Factories;

use Attribute as BaseAttribute;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use Smpl\Inspector\Collections;
use Smpl\Inspector\Concerns;
use Smpl\Inspector\Contracts;
use Smpl\Inspector\Elements;
use Smpl\Inspector\Exceptions;
use Smpl\Inspector\Support\AttributeTarget;
use Smpl\Inspector\Support\StructureType;

class StructureFactory implements Contracts\StructureFactory
{
    use Concerns\CachesStructures,
        Concerns\CachesProperties,
        Concerns\CachesMethods,
        Concerns\CachesAttributes;

    private static self $instance;

    public static function getInstance(?TypeFactory $factory = null): static
    {
        if (! isset(self::$instance)) {
            self::$instance = new self($factory);
        }

        return self::$instance;
    }

    /**
     * @param class-string|string $class
     *
     * @return bool
     */
    public static function isValidClass(string $class): bool
    {
        return class_exists($class)
            || interface_exists($class)
            || trait_exists($class)
            || enum_exists($class);
    }

    private Contracts\TypeFactory $typeFactory;

    private function __construct(?Contracts\TypeFactory $typeFactory = null)
    {
        $this->typeFactory = $typeFactory ?? TypeFactory::getInstance();
    }

    private function __clone(): void
    {
        // This method is intentionally empty
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

    private function getType(?ReflectionType $type = null): ?Contracts\Type
    {
        if ($type === null) {
            return null;
        }

        return $this->typeFactory->make($type);
    }

    private function makeStructureFromReflection(ReflectionClass $reflection): Contracts\Structure
    {
        return $this->addStructure(new Elements\Structure(
            $reflection,
            $this->getStructureType($reflection),
            $this->typeFactory->make($reflection->getName())
        ));
    }

    /**
     * Get an instance of PHPs base attribute class for the provided attribute.
     *
     * @param class-string $class
     *
     * @return \Attribute
     *
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    private function makeBaseAttribute(string $class): BaseAttribute
    {
        try {
            $reflection    = new ReflectionClass($class);
            $baseAttribute = $reflection->getAttributes(
                    BaseAttribute::class, ReflectionAttribute::IS_INSTANCEOF
                )[0] ?? null;
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            throw Exceptions\StructureException::invalidClass($class, $e);
        }
        // @codeCoverageIgnoreEnd

        if ($baseAttribute === null) {
            throw Exceptions\AttributeException::invalidAttribute($class);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $baseAttribute->newInstance();
    }

    /**
     * @param list<\Smpl\Inspector\Contracts\Metadata> $metadata
     * @param \Smpl\Inspector\Support\AttributeTarget  $target
     *
     * @return void
     *
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    private function validateMetadata(array $metadata, AttributeTarget $target): void
    {
        $attributes = [];

        foreach ($metadata as $metadatum) {
            $attribute = $metadatum->getAttribute();

            if (! in_array($target, $attribute->getTargets())) {
                throw Exceptions\AttributeException::invalidTarget($attribute->getName(), $target);
            }

            if ($attribute->isRepeatable()) {
                continue;
            }

            if (in_array($attribute->getName(), $attributes, true)) {
                throw Exceptions\AttributeException::nonRepeatableAttribute($attribute->getName());
            }

            $attributes[] = $attribute->getName();
        }
    }

    private function makeInheritedMethods(Contracts\Structure $structure, ReflectionMethod ...$methods): Contracts\MethodCollection
    {
        $array = [];

        foreach ($methods as $method) {
            $element = $this->makeMethod($method);

            if ($element->getStructure()->getFullName() !== $structure->getFullName()) {
                $element = new Elements\InheritedMethod($structure, $element);
            }

            $array[] = $element;
        }

        return new Collections\Methods($array);
    }

    private function makeInheritedProperties(Contracts\Structure $structure, ReflectionProperty ...$properties): Contracts\PropertyCollection
    {
        $array = [];

        foreach ($properties as $property) {
            $element = $this->makeProperty($property);

            if ($element->getStructure()->getFullName() !== $structure->getFullName()) {
                $element = new Elements\InheritedProperty($structure, $element);
            }

            $array[] = $element;
        }

        return new Collections\Properties($array);
    }

    /**
     * @psalm-suppress NullableReturnStatement
     * @psalm-suppress InvalidNullableReturnType
     */
    public function makeStructure(object|string $class): Contracts\Structure
    {
        // Get the name from the object if one was passed
        $name = is_object($class) ? $class::class : $class;

        if (! self::isValidClass($name)) {
            /** @infection-ignore-all  */
            throw Exceptions\StructureException::invalidClass($name);
        }

        // Check if we've already cached this structure
        if ($this->hasStructure($name)) {
            return $this->getStructure($name);
        }

        try {
            return $this->makeStructureFromReflection(new ReflectionClass($name));
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            throw Exceptions\StructureException::invalidClass($name, $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function makeStructures(object|string ...$classes): Contracts\StructureCollection
    {
        $array = [];

        foreach ($classes as $class) {
            if ($class instanceof ReflectionClass) {
                try {
                    $array[] = $this->makeStructureFromReflection($class);
                    // @codeCoverageIgnoreStart
                } catch (ReflectionException $e) {
                    throw Exceptions\StructureException::invalidClass($class->getName(), $e);
                }
                // @codeCoverageIgnoreEnd
            } else {
                $array[] = $this->makeStructure($class);
            }
        }

        return new Collections\Structures($array);
    }

    /**
     * @psalm-suppress NullableReturnStatement
     * @psalm-suppress InvalidNullableReturnType
     */
    public function makeProperty(ReflectionProperty $property): Contracts\Property
    {
        $class = $property->getDeclaringClass()->getName();
        $name  = $property->getName();

        // Check if we've already cached this property
        if ($this->hasProperty($class, $name)) {
            return $this->getProperty($class, $name);
        }

        return $this->addProperty(new Elements\Property(
            $this->makeStructure($class),
            $property,
            $this->getType($property->getType())
        ));
    }

    public function makeProperties(ReflectionProperty ...$properties): Contracts\PropertyCollection
    {
        $array = [];

        foreach ($properties as $property) {
            $array[] = $this->makeProperty($property);
        }

        return new Collections\Properties($array);
    }

    public function makeStructureProperties(Contracts\Structure $structure): Contracts\StructurePropertyCollection
    {
        if (! $structure->getStructureType()->canHaveProperties()) {
            throw Exceptions\StructureException::noProperties(
                $structure->getFullName(),
                $structure->getStructureType()->name
            );
        }

        return Collections\StructureProperties::for(
            $structure,
            $this->makeInheritedProperties($structure, ...$structure->getReflection()->getProperties())
        );
    }

    /**
     * @psalm-suppress NullableReturnStatement
     * @psalm-suppress InvalidNullableReturnType
     */
    public function makeMethod(ReflectionMethod $method): Contracts\Method
    {
        $class = $method->class;
        $name  = $method->name;

        // Check if we've already cached this property
        if ($this->hasMethod($class, $name)) {
            return $this->getMethod($class, $name);
        }

        return $this->addMethod(new Elements\Method(
            $this->makeStructure($class),
            $method,
            $this->getType($method->getReturnType())
        ));
    }

    public function makeMethods(ReflectionMethod ...$methods): Contracts\MethodCollection
    {
        $array = [];

        foreach ($methods as $method) {
            $array[] = $this->makeMethod($method);
        }

        return new Collections\Methods($array);
    }

    public function makeStructureMethods(Contracts\Structure $structure): Contracts\StructureMethodCollection
    {
        return Collections\StructureMethods::for(
            $structure,
            $this->makeInheritedMethods($structure, ...$structure->getReflection()->getMethods())
        );
    }

    public function makeParameter(ReflectionParameter $reflection): Contracts\Parameter
    {
        $parentReflection = $reflection->getDeclaringFunction();

        if (! ($parentReflection instanceof ReflectionMethod)) {
            throw Exceptions\StructureException::functions();
        }

        return new Elements\Parameter(
            $this->makeMethod($parentReflection),
            $reflection,
            $this->getType($reflection->getType())
        );
    }

    public function makeParameters(ReflectionParameter ...$parameters): Contracts\ParameterCollection
    {
        $array = [];

        foreach ($parameters as $parameter) {
            $array[] = $this->makeParameter($parameter);
        }

        return new Collections\Parameters($array);
    }

    public function makeMethodParameters(Contracts\Method $method): Contracts\MethodParameterCollection
    {
        return Collections\MethodParameters::for(
            $method,
            $this->makeParameters(...$method->getReflection()->getParameters())
        );
    }

    /**
     * @psalm-suppress NullableReturnStatement
     * @psalm-suppress InvalidNullableReturnType
     */
    public function makeAttribute(string $class): Contracts\Attribute
    {
        if ($class === BaseAttribute::class) {
            throw Exceptions\AttributeException::baseAttribute();
        }

        if (! self::isValidClass($class)) {
            /** @infection-ignore-all  */
            throw Exceptions\StructureException::invalidClass($class);
        }

        if ($this->hasAttribute($class)) {
            return $this->getAttribute($class);
        }

        return $this->addAttribute(new Elements\Attribute(
            $class,
            $this->makeBaseAttribute($class)
        ));
    }

    public function makeMetadatum(ReflectionAttribute $reflection): Contracts\Metadata
    {
        return new Elements\Metadata(
            $this->makeAttribute($reflection->getName()),
            $reflection
        );
    }

    public function makeMetadata(ReflectionAttribute ...$reflections): array
    {
        $metadata = [];

        foreach ($reflections as $reflection) {
            if ($reflection->getName() === BaseAttribute::class) {
                continue;
            }

            $metadata[] = $this->makeMetadatum($reflection);
        }

        return $metadata;
    }

    public function makeStructureMetadata(Contracts\Structure $structure): Contracts\StructureMetadataCollection
    {
        $metadata = $this->makeMetadata(...$structure->getReflection()->getAttributes());
        $this->validateMetadata($metadata, AttributeTarget::Structure);

        return new Collections\StructureMetadata(
            $structure,
            $metadata
        );
    }

    public function makePropertyMetadata(Contracts\Property $property): Contracts\PropertyMetadataCollection
    {
        $metadata = $this->makeMetadata(...$property->getReflection()->getAttributes());
        $this->validateMetadata($metadata, AttributeTarget::Property);

        return new Collections\PropertyMetadata(
            $property,
            $metadata
        );
    }

    public function makeMethodMetadata(Contracts\Method $method): Contracts\MethodMetadataCollection
    {
        $metadata = $this->makeMetadata(...$method->getReflection()->getAttributes());
        $this->validateMetadata($metadata, AttributeTarget::Method);

        return new Collections\MethodMetadata(
            $method,
            $metadata
        );
    }

    public function makeParameterMetadata(Contracts\Parameter $parameter): Contracts\ParameterMetadataCollection
    {
        $metadata = $this->makeMetadata(...$parameter->getReflection()->getAttributes());
        $this->validateMetadata($metadata, AttributeTarget::Parameter);

        return new Collections\ParameterMetadata(
            $parameter,
            $metadata
        );
    }
}