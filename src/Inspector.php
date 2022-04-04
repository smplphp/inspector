<?php

declare(strict_types=1);

namespace Smpl\Inspector;

use Smpl\Inspector\Mappers\ComposerMapper;

final class Inspector
{
    private static ?self $instance;

    /**
     * @param \Smpl\Inspector\Contracts\TypeFactory|null      $types
     * @param \Smpl\Inspector\Contracts\StructureFactory|null $structures
     * @param \Smpl\Inspector\Contracts\Mapper|null           $mapper
     *
     * @return static
     *
     * @psalm-suppress NullableReturnStatement
     * @psalm-suppress InvalidNullableReturnType
     */
    public static function getInstance(
        ?Contracts\TypeFactory      $types = null,
        ?Contracts\StructureFactory $structures = null,
        Contracts\Mapper            $mapper = null
    ): self
    {
        if (! isset(self::$instance)) {
            self::setInstance(new self($types, $structures, $mapper));
        }

        return self::$instance;
    }

    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    private Contracts\TypeFactory $types;

    private Contracts\StructureFactory $structures;

    private Contracts\Mapper $mapper;

    public function __construct(
        ?Contracts\TypeFactory      $types = null,
        ?Contracts\StructureFactory $structures = null,
        Contracts\Mapper            $mapper = null
    )
    {
        $this->types      = $types ?? Factories\TypeFactory::getInstance();
        $this->structures = $structures ?? Factories\StructureFactory::getInstance();
        $this->mapper     = $mapper ?? new ComposerMapper;
    }

    /**
     * @return \Smpl\Inspector\Contracts\TypeFactory
     *
     * @codeCoverageIgnore This is a singletons so it's hard to test
     */
    public function getTypeFactory(): Contracts\TypeFactory
    {
        return $this->types;
    }

    /**
     * @return \Smpl\Inspector\Contracts\StructureFactory
     *
     * @codeCoverageIgnore This is a singletons so it's hard to test
     */
    public function getStructureFactory(): Contracts\StructureFactory
    {
        return $this->structures;
    }

    /**
     * @return \Smpl\Inspector\Contracts\Mapper
     */
    public function getMapper(): Contracts\Mapper
    {
        return $this->mapper;
    }

    public function inspect(): Inspection
    {
        return new Inspection($this);
    }

    /**
     * @param class-string $className
     *
     * @return \Smpl\Inspector\Contracts\Structure|null
     *
     * @throws \Smpl\Inspector\Exceptions\InspectionException
     */
    public function inspectClass(string $className): ?Contracts\Structure
    {
        return $this->inspect()
                    ->inClass($className)
                    ->getStructures()
                    ->first();
    }

    /**
     * @param class-string $className
     * @param string       $methodName
     *
     * @return \Smpl\Inspector\Contracts\Method|null
     *
     * @throws \Smpl\Inspector\Exceptions\InspectionException
     */
    public function inspectMethod(string $className, string $methodName): ?Contracts\Method
    {
        /** @infection-ignore-all  */
        return $this->inspectClass($className)?->getMethod($methodName);
    }
}