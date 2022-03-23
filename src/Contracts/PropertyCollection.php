<?php

namespace Smpl\Inspector\Contracts;

/**
 * Property Collection Contract
 *
 * This contract represents a collection of structure properties.
 *
 * @extends \Smpl\Inspector\Contracts\Collection<string, \Smpl\Inspector\Contracts\Property>
 */
interface PropertyCollection extends Collection
{
    /**
     * Get a property by the provided name.
     *
     * @param string $name
     *
     * @return \Smpl\Inspector\Contracts\Property|null
     */
    public function get(string $name): ?Property;

    /**
     * Check if the collection contains a property by the provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get the property at the provided index.
     *
     * @param int $index
     *
     * @return \Smpl\Inspector\Contracts\Property|null
     */
    public function indexOf(int $index): ?Property;

    /**
     * Get the first property from this collection.
     *
     * @return \Smpl\Inspector\Contracts\Property|null
     */
    public function first(): ?Property;

    /**
     * Create a new filtered collection using the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\PropertyFilter $filter
     *
     * @return static
     */
    public function filter(PropertyFilter $filter): static;

    /**
     * Get the names of all the properties contained in this collection.
     *
     * If $includeClass is true, all names will contain the name of the class,
     * otherwise, it will just contain the name of the property.
     *
     * @param bool $includeClass
     *
     * @return string[]
     */
    public function names(bool $includeClass = true): array;

    /**
     * Get an array of properties.
     *
     * @return list<\Smpl\Inspector\Contracts\Property>
     */
    public function values(): array;
}