<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\ParameterFilter;
use Traversable;

final class MethodParameters implements MethodParameterCollection
{
    /**
     * @param \Smpl\Inspector\Contracts\Parameter[] $parameters
     *
     * @return array<string, \Smpl\Inspector\Contracts\Parameter>
     */
    private static function keyByName(array $parameters): array
    {
        $keyed = [];

        foreach ($parameters as $parameter) {
            $keyed[$parameter->getName()] = $parameter;
        }

        return $keyed;
    }

    /**
     * @param \Smpl\Inspector\Contracts\Parameter[] $parameters
     *
     * @return array<int, string>
     */
    private static function mapPositionsToName(array $parameters): array
    {
        $positions = [];

        foreach ($parameters as $parameter) {
            $positions[$parameter->getPosition()] = $parameter->getName();
        }

        ksort($positions);

        return $positions;
    }

    private Method $method;

    /**
     * @var array<string, \Smpl\Inspector\Contracts\Parameter>
     */
    private array $parameters;

    /**
     * @var array<int, string>
     */
    private array $positions;

    /**
     * @param \Smpl\Inspector\Contracts\Method      $method
     * @param \Smpl\Inspector\Contracts\Parameter[] $parameters
     */
    public function __construct(Method $method, array $parameters)
    {
        $this->method     = $method;
        $this->parameters = self::keyByName($parameters);
        $this->positions  = self::mapPositionsToName($this->parameters);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->parameters);
    }

    public function count(): int
    {
        return count($this->parameters);
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function get(int|string $name): ?Parameter
    {
        if (! is_string($name)) {
            if (! isset($this->positions[$name])) {
                return null;
            }

            $name = $this->positions[$name];
        }

        return $this->parameters[$name] ?? null;
    }

    public function has(int|string $name): bool
    {
        return $this->get($name) !== null;
    }

    public function filter(ParameterFilter $filter): MethodParameterCollection
    {
        return new self(
            $this->getMethod(),
            array_filter($this->parameters, $filter->check(...))
        );
    }
}