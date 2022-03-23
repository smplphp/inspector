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
use Smpl\Inspector\Support\StructureType;

class StructureFactory implements Contracts\StructureFactory
{
    use Concerns\CachesStructures,
        Concerns\CachesProperties,
        Concerns\CachesMethods,
        Concerns\CachesAttributes;

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

    public function __construct(Contracts\TypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
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
        } catch (ReflectionException $e) {
            throw Exceptions\StructureException::invalidClass($class, $e);
        }

        if ($baseAttribute === null) {
            throw Exceptions\AttributeException::invalidAttribute($class);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $baseAttribute->newInstance();
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
        return Collections\StructureProperties::for(
            $structure,
            $this->makeProperties(...$structure->getReflection()->getProperties())
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
            $this->makeMethods(...$structure->getReflection()->getMethods())
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
        return new Collections\StructureMetadata(
            $structure,
            $this->makeMetadata(...$structure->getReflection()->getAttributes())
        );
    }

    public function makePropertyMetadata(Contracts\Property $property): Contracts\PropertyMetadataCollection
    {
        return new Collections\PropertyMetadata(
            $property,
            $this->makeMetadata(...$property->getReflection()->getAttributes())
        );
    }

    public function makeMethodMetadata(Contracts\Method $method): Contracts\MethodMetadataCollection
    {
        return new Collections\MethodMetadata(
            $method,
            $this->makeMetadata(...$method->getReflection()->getAttributes())
        );
    }

    public function makeParameterMetadata(Contracts\Parameter $parameter): Contracts\ParameterMetadataCollection
    {
        return new Collections\ParameterMetadata(
            $parameter,
            $this->makeMetadata(...$parameter->getReflection()->getAttributes())
        );
    }
}