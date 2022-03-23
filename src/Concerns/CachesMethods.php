<?php

declare(strict_types=1);

namespace Smpl\Inspector\Concerns;

use Smpl\Inspector\Contracts\Method;

trait CachesMethods
{
    /**
     * @var array<class-string, array<string, \Smpl\Inspector\Contracts\Method>>
     */
    private array $methods = [];

    /**
     * Get an already created class method.
     *
     * @param class-string $class
     * @param string       $name
     *
     * @return \Smpl\Inspector\Contracts\Method|null
     */
    protected function getMethod(string $class, string $name): ?Method
    {
        return ($this->methods[$class] ?? [])[$name] ?? null;
    }

    /**
     * Check if a class method has already been created.
     *
     * @param class-string $class
     * @param string       $name
     *
     * @return bool
     */
    protected function hasMethod(string $class, string $name): bool
    {
        return $this->getMethod($class, $name) !== null;
    }

    /**
     * Cache a created method and return it.
     *
     * @param \Smpl\Inspector\Contracts\Method $method
     *
     * @return \Smpl\Inspector\Contracts\Method
     */
    protected function addMethod(Method $method): Method
    {
        $this->methods[$method->getStructure()->getFullName()][$method->getName()] = $method;
        return $method;
    }
}