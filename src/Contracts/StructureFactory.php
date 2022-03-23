<?php

namespace Smpl\Inspector\Contracts;

use ReflectionAttribute;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Structure Factory Contract
 *
 * This contract represents the factory used to create structures and their
 * subsequent elements.
 *
 * @see \Smpl\Inspector\Contracts\Structure
 * @see \Smpl\Inspector\Contracts\Method
 * @see \Smpl\Inspector\Contracts\Attribute
 * @see \Smpl\Inspector\Contracts\Metadata
 * @see \Smpl\Inspector\Contracts\Property
 * @see \Smpl\Inspector\Contracts\Parameter
 */
interface StructureFactory
{
    /**
     * Make a structure for the provided class name or object.
     *
     * @psalm-template S of object
     * @psalm-param S|class-string<S> $class
     *
     * @param object|class-string     $class
     *
     * @return \Smpl\Inspector\Contracts\Structure
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeStructure(object|string $class): Structure;

    /**
     * Make a property from its reflection.
     *
     * @param \ReflectionProperty $property
     *
     * @return \Smpl\Inspector\Contracts\Property
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeProperty(ReflectionProperty $property): Property;

    /**
     * Make a collection of properties.
     *
     * @param \ReflectionProperty ...$properties
     *
     * @return \Smpl\Inspector\Contracts\PropertyCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeProperties(ReflectionProperty ...$properties): PropertyCollection;

    /**
     * Make a collection of structure properties for the provided structure.
     *
     * @param \Smpl\Inspector\Contracts\Structure $structure
     *
     * @return \Smpl\Inspector\Contracts\StructurePropertyCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeStructureProperties(Structure $structure): StructurePropertyCollection;

    /**
     * Make a method from its reflection.
     *
     * @param \ReflectionMethod $method
     *
     * @return \Smpl\Inspector\Contracts\Method
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeMethod(ReflectionMethod $method): Method;

    /**
     * Make a collection of methods.
     *
     * @param \ReflectionMethod ...$methods
     *
     * @return \Smpl\Inspector\Contracts\MethodCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeMethods(ReflectionMethod ...$methods): MethodCollection;

    /**
     * Make a collection of structure methods for the provided structure.
     *
     * @param \Smpl\Inspector\Contracts\Structure $structure
     *
     * @return \Smpl\Inspector\Contracts\StructureMethodCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeStructureMethods(Structure $structure): StructureMethodCollection;

    /**
     * Make a parameter from its reflection.
     *
     * @param \ReflectionParameter $reflection
     *
     * @return \Smpl\Inspector\Contracts\Parameter
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeParameter(ReflectionParameter $reflection): Parameter;

    /**
     * Make a collection of parameters.
     *
     * @param \ReflectionParameter ...$parameters
     *
     * @return \Smpl\Inspector\Contracts\ParameterCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeParameters(ReflectionParameter ...$parameters): ParameterCollection;

    /**
     * Make a collection of method parameters for the provided method.
     *
     * @param \Smpl\Inspector\Contracts\Method $method
     *
     * @return \Smpl\Inspector\Contracts\MethodParameterCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     */
    public function makeMethodParameters(Method $method): MethodParameterCollection;

    /**
     * Make an attribute from its class name.
     *
     * @param class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Attribute
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    public function makeAttribute(string $class): Attribute;

    /**
     * Make a metadata instance from its reflection.
     *
     * @param \ReflectionAttribute $reflection
     *
     * @return \Smpl\Inspector\Contracts\Metadata
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    public function makeMetadatum(ReflectionAttribute $reflection): Metadata;

    /**
     * Make a collection of metadata.
     *
     * @param \ReflectionAttribute ...$reflections
     *
     * @return list<\Smpl\Inspector\Contracts\Metadata>
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    public function makeMetadata(ReflectionAttribute ...$reflections): array;

    /**
     * Make a collection of metadata for the provided structure.
     *
     * @param \Smpl\Inspector\Contracts\Structure $structure
     *
     * @return \Smpl\Inspector\Contracts\StructureMetadataCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    public function makeStructureMetadata(Structure $structure): StructureMetadataCollection;

    /**
     * Make a collection of metadata for the provided property.
     *
     * @param \Smpl\Inspector\Contracts\Property $property
     *
     * @return \Smpl\Inspector\Contracts\PropertyMetadataCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    public function makePropertyMetadata(Property $property): PropertyMetadataCollection;

    /**
     * Make a collection of metadata for the provided method.
     *
     * @param \Smpl\Inspector\Contracts\Method $method
     *
     * @return \Smpl\Inspector\Contracts\MethodMetadataCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    public function makeMethodMetadata(Method $method): MethodMetadataCollection;

    /**
     * Make a collection of metadata for the provided parameter.
     *
     * @param \Smpl\Inspector\Contracts\Parameter $parameter
     *
     * @return \Smpl\Inspector\Contracts\ParameterMetadataCollection
     *
     * @throws \Smpl\Inspector\Exceptions\StructureException
     * @throws \Smpl\Inspector\Exceptions\AttributeException
     */
    public function makeParameterMetadata(Parameter $parameter): ParameterMetadataCollection;
}