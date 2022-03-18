<?php

namespace Smpl\Inspector\Contracts;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

interface StructureFactory
{
    /**
     * @param \ReflectionClass|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function makeStructure(ReflectionClass|string $class): Structure;

    /**
     * @param \ReflectionProperty|string                                        $property
     * @param \ReflectionClass|\Smpl\Inspector\Contracts\Structure|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Property
     */
    public function makeProperty(ReflectionProperty|string $property, ReflectionClass|Structure|string $class): Property;

    /**
     * @param \ReflectionClass|\Smpl\Inspector\Contracts\Structure|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\StructurePropertyCollection
     */
    public function makeProperties(ReflectionClass|Structure|string $class): StructurePropertyCollection;

    /**
     * @param \ReflectionMethod|string                                          $method
     * @param \ReflectionClass|\Smpl\Inspector\Contracts\Structure|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Method
     */
    public function makeMethod(ReflectionMethod|string $method, ReflectionClass|Structure|string $class): Method;

    /**
     * @param \ReflectionClass|\Smpl\Inspector\Contracts\Structure|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\StructureMethodCollection
     */
    public function makeMethods(ReflectionClass|Structure|string $class): StructureMethodCollection;

    /**
     * @param \ReflectionParameter                                              $parameter
     * @param \ReflectionMethod|\Smpl\Inspector\Contracts\Method|string         $method
     * @param \ReflectionClass|\Smpl\Inspector\Contracts\Structure|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Parameter
     */
    public function makeParameter(ReflectionParameter $parameter, ReflectionMethod|Method|string $method, ReflectionClass|Structure|string $class): Parameter;

    /**
     * @param \ReflectionMethod|\Smpl\Inspector\Contracts\Method|string              $method
     * @param \ReflectionClass|\Smpl\Inspector\Contracts\Structure|class-string|null $class
     *
     * @return \Smpl\Inspector\Contracts\MethodParameterCollection
     */
    public function makeParameters(ReflectionMethod|Method|string $method, ReflectionClass|Structure|string|null $class = null): MethodParameterCollection;

    public function makeAttribute(ReflectionAttribute $reflection): Attribute;

    public function makeStructureAttributes(Structure $structure): StructureAttributeCollection;

    public function makePropertyAttributes(Property $property): PropertyAttributeCollection;

    public function makeMethodAttributes(Method $method): MethodAttributeCollection;

    public function makeParameterAttributes(Parameter $parameter): ParameterAttributeCollection;

    public function makeMetadata(Attribute $attribute, ReflectionAttribute $reflection): Metadata;
}