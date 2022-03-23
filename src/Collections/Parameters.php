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
     * @var array<string, \Smpl\Inspector\Contracts\Parameter>
     */
    protected array $parameters;

    /**
     * @var array<int, string>
     */
    private array $positions;

    /**
     * @param list<\Smpl\Inspector\Contracts\Parameter> $parameters
     */
    public function __construct(array $parameters)
    {
        $this->buildParametersAndPositions($parameters);
    }

    /**
     * Build the parameters and index properties.
     *
     * @param list<\Smpl\Inspector\Contracts\Parameter> $parameters
     *
     * @return void
     */
    private function buildParametersAndPositions(array $parameters): void
    {
        $this->parameters = [];
        $this->positions  = [];

        foreach ($parameters as $parameter) {
            $this->parameters[$parameter->getName()]    = $parameter;
            $this->positions[$parameter->getPosition()] = $parameter->getName();
        }

        ksort($this->positions, SORT_NUMERIC);
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

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function filter(ParameterFilter $filter): static
    {
        $filtered = clone $this;
        $filtered->buildParametersAndPositions(array_filter($this->values(), $filter->check(...)));

        return $filtered;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function indexOf(int $position): ?Parameter
    {
        if (! isset($this->positions[$position])) {
            return null;
        }

        return $this->get($this->positions[$position]);
    }

    public function first(): ?Parameter
    {
        return $this->indexOf(0);
    }

    public function names(): array
    {
        return array_values($this->positions);
    }

    public function values(): array
    {
        return array_values($this->parameters);
    }
}