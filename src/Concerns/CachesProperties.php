<?php

declare(strict_types=1);

namespace Smpl\Inspector\Concerns;

use Smpl\Inspector\Contracts\Property;

trait CachesProperties
{
    /**
     * @var array<class-string, array<string, \Smpl\Inspector\Contracts\Property>>
     */
    private array $properties = [];

    /**
     * Get an already created class property.
     *
     * @param class-string $class
     * @param string       $name
     *
     * @return \Smpl\Inspector\Contracts\Property|null
     */
    protected function getProperty(string $class, string $name): ?Property
    {
        return ($this->properties[$class] ?? [])[$name] ?? null;
    }

    /**
     * Check if a class property has already been created.
     *
     * @param class-string $class
     * @param string       $name
     *
     * @return bool
     */
    protected function hasProperty(string $class, string $name): bool
    {
        return $this->getProperty($class, $name) !== null;
    }

    /**
     * Cache a created property and return it.
     *
     * @param \Smpl\Inspector\Contracts\Property $property
     *
     * @return \Smpl\Inspector\Contracts\Property
     */
    protected function addProperty(Property $property): Property
    {
        $this->properties[$property->getStructure()->getFullName()][$property->getName()] = $property;
        return $property;
    }
}