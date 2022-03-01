<?php

declare(strict_types=1);

namespace Smpl\Inspector\Factories;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Smpl\Inspector\Contracts\Structure as StructureContract;
use Smpl\Inspector\Contracts\StructureFactory as StructureFactoryContract;
use Smpl\Inspector\Contracts\TypeFactory;
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

    public function __construct(TypeFactory $types)
    {
        $this->types = $types;
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
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function make(ReflectionClass|string $class): StructureContract
    {
        $className = is_string($class) ? $class : $class->getName();

        if ($this->hasStructure($className)) {
            return $this->getStructure($className);
        }

        if ($class instanceof ReflectionClass) {
            return $this->makeFromReflection($class);
        }

        if ($this->isValidClass($class)) {
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
}