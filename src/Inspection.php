<?php

declare(strict_types=1);

namespace Smpl\Inspector;

use Smpl\Inspector\Collections\Methods;
use Smpl\Inspector\Collections\Properties;
use Smpl\Inspector\Contracts\Mapper;
use Smpl\Inspector\Contracts\MethodCollection;
use Smpl\Inspector\Contracts\PropertyCollection;
use Smpl\Inspector\Contracts\StructureCollection;
use Smpl\Inspector\Exceptions\InspectionException;
use Smpl\Inspector\Exceptions\InspectorException;
use Smpl\Inspector\Filters\MethodFilter;
use Smpl\Inspector\Filters\PropertyFilter;
use Smpl\Inspector\Filters\StructureFilter;

class Inspection
{
    private Inspector $inspector;

    /**
     * @var list<class-string>
     */
    private array $classes;

    /**
     * @var list<string>
     */
    private array $paths;

    /**
     * @var list<string>
     */
    private array $namespaces;

    private Mapper $mapper;

    private StructureFilter $structureFilter;

    public function __construct(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }

    /**
     * @param object|class-string $class
     *
     * @return static
     */
    public function inClass(object|string $class): static
    {
        return $this->inClasses($class);
    }

    public function inPath(string $path): static
    {
        return $this->inPaths($path);
    }

    public function inNamespace(string $namespace): static
    {
        return $this->inNamespaces($namespace);
    }

    /**
     * @param object|class-string ...$classes
     *
     * @return static
     */
    public function inClasses(object|string ...$classes): static
    {
        /**
         * @var list<class-string|object> $classes
         */

        foreach ($classes as $class) {
            $this->classes[] = is_object($class) ? $class::class : $class;
        }

        return $this;
    }

    /**
     * @param string ...$paths
     *
     * @return static
     */
    public function inPaths(string ...$paths): static
    {
        /**
         * @var list<string> $paths
         */
        $this->paths = $paths;
        return $this;
    }

    /**
     * @param string ...$namespaces
     *
     * @return static
     */
    public function inNamespaces(string ...$namespaces): static
    {
        /**
         * @var list<string> $namespaces
         */
        $this->namespaces = $namespaces;
        return $this;
    }

    public function usingMapper(Mapper $mapper): static
    {
        $this->mapper = $mapper;
        return $this;
    }

    public function where(StructureFilter $structureFilter): static
    {
        $this->structureFilter = $structureFilter;
        return $this;
    }

    /**
     * @throws \Smpl\Inspector\Exceptions\InspectionException
     */
    public function getStructures(): StructureCollection
    {
        return $this->run();
    }

    /**
     * @throws \Smpl\Inspector\Exceptions\InspectionException
     */
    public function getMethods(?MethodFilter $methodFilter = null): MethodCollection
    {
        $methods    = [];
        $structures = $this->getStructures();

        foreach ($structures as $structure) {
            $structureMethods = $structure->getMethods();

            if ($methodFilter !== null) {
                $structureMethods = $structureMethods->filter($methodFilter);
            }

            $methods[] = $structureMethods->values();
        }

        return new Methods(array_merge(...$methods));
    }

    /**
     * @throws \Smpl\Inspector\Exceptions\InspectionException
     */
    public function getProperties(?PropertyFilter $propertyFilter = null): PropertyCollection
    {
        $properties = [];
        $structures = $this->getStructures();

        foreach ($structures as $structure) {
            $structureProperties = $structure->getProperties();

            if ($propertyFilter !== null) {
                $structureProperties = $structureProperties->filter($propertyFilter);
            }

            $properties[] = $structureProperties->values();
        }

        return new Properties(array_merge(...$properties));
    }

    /**
     * @throws \Smpl\Inspector\Exceptions\InspectionException
     */
    private function run(): StructureCollection
    {
        try {
            if (isset($this->paths)) {
                $classes = $this->mapPaths();
            } else if (isset($this->classes)) {
                $classes = $this->classes;
            } else if (isset($this->namespaces)) {
                $classes = $this->mapNamespaces();
            } else {
                throw InspectionException::noSource();
            }
        } catch (InspectionException $exception) {
            throw $exception;
        } catch (InspectorException $exception) {
            throw InspectionException::somethingWrong($exception);
        }

        $structures = $this->inspector->getStructureFactory()
                                      ->makeStructures(...$classes);

        if (isset($this->structureFilter)) {
            $structures = $structures->filter($this->structureFilter);
        }

        return $structures;
    }

    /**
     * @return list<class-string>
     *
     * @throws \Smpl\Inspector\Exceptions\MapperException
     */
    private function mapPaths(): array
    {
        $mapper  = $this->mapper ?? $this->inspector->getMapper();
        $classes = [];

        foreach ($this->paths as $path) {
            $classes[] = $mapper->mapPath($path);
        }

        return array_merge(...$classes);
    }

    /**
     * @return list<class-string>
     */
    private function mapNamespaces(): array
    {
        $mapper  = $this->mapper ?? $this->inspector->getMapper();
        $classes = [];

        foreach ($this->namespaces as $namespace) {
            $classes[] = $mapper->mapNamespace($namespace);
        }

        return array_merge(...$classes);
    }
}