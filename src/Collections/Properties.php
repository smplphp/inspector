<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyCollection;
use Smpl\Inspector\Contracts\PropertyFilter;
use Smpl\Inspector\Contracts\Structure;
use Traversable;

class Properties implements PropertyCollection
{
    /**
     * @var array<string, \Smpl\Inspector\Contracts\Property>
     */
    protected array $properties;

    /**
     * @var array<int, string>
     */
    private array $indexes;

    /**
     * @param list<\Smpl\Inspector\Contracts\Property> $properties
     */
    public function __construct(array $properties)
    {
        $this->buildPropertiesAndIndexes($properties);
    }

    /**
     * Build the properties and index properties.
     *
     * @param list<\Smpl\Inspector\Contracts\Property> $properties
     *
     * @return void
     */
    private function buildPropertiesAndIndexes(array $properties): void
    {
        $this->properties = [];
        $this->indexes    = [];

        foreach ($properties as $property) {
            $this->properties[$property->getFullName()] = $property;
            $this->indexes[]                            = $property->getFullName();
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->properties);
    }

    public function count(): int
    {
        return count($this->properties);
    }

    public function get(string $name): ?Property
    {
        return $this->properties[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function filter(PropertyFilter $filter): static
    {
        $filtered = clone $this;
        $filtered->buildPropertiesAndIndexes(array_filter($this->values(), $filter->check(...)));

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

    public function indexOf(int $index): ?Property
    {
        if (! isset($this->indexes[$index])) {
            return null;
        }

        return $this->get($this->indexes[$index]);
    }

    public function first(): ?Property
    {
        return $this->indexOf(0);
    }

    public function names(bool $includeClass = true): array
    {
        $names = $this->indexes;

        if (! $includeClass) {
            $names = array_map(
                static function (string $name) {
                    return str_contains($name, Structure::SEPARATOR)
                        ? explode(Structure::SEPARATOR, $name)[1]
                        : $name;
                },
                $names
            );
        }

        return array_unique($names);
    }

    public function values(): array
    {
        return array_values($this->properties);
    }
}