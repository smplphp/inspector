<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\ParameterCollection;
use Smpl\Inspector\Contracts\ParameterFilter;
use Traversable;

class Parameters implements ParameterCollection
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

    /**
     * @var array<string, \Smpl\Inspector\Contracts\Parameter>
     */
    protected array $parameters;

    /**
     * @var array<int, string>
     */
    private array $positions;

    /**
     * @param \Smpl\Inspector\Contracts\Parameter[] $parameters
     */
    public function __construct(array $parameters)
    {
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

    public function filter(ParameterFilter $filter): static
    {
        return new self(
            array_filter($this->parameters, $filter->check(...))
        );
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
}