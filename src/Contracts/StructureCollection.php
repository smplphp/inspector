<?php

namespace Smpl\Inspector\Contracts;

/**
 * Structure Collection Contract
 *
 * This contract represents a collection of structures.
 *
 * @extends \Smpl\Inspector\Contracts\Collection<string, \Smpl\Inspector\Contracts\Structure>
 */
interface StructureCollection extends Collection
{
    /**
     * Get a structure by the provided name.
     *
     * @param class-string $name
     *
     * @return \Smpl\Inspector\Contracts\Structure|null
     */
    public function get(string $name): ?Structure;

    /**
     * Check if the collection contains a structure by the provided name.
     *
     * @param class-string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get the structure at the provided index.
     *
     * @param int $index
     *
     * @return \Smpl\Inspector\Contracts\Structure|null
     */
    public function indexOf(int $index): ?Structure;

    /**
     * Get the first structure from this collection.
     *
     * @return \Smpl\Inspector\Contracts\Structure|null
     */
    public function first(): ?Structure;

    /**
     * Create a new filtered collection using the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\StructureFilter $filter
     *
     * @return static
     */
    public function filter(StructureFilter $filter): static;

    /**
     * Get the names of all the structures contained in this collection.
     *
     * @return class-string[]
     */
    public function names(): array;

    /**
     * Get an array of structures.
     *
     * @return list<\Smpl\Inspector\Contracts\Structure>
     */
    public function values(): array;
}