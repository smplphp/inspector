<?php

namespace Smpl\Inspector\Contracts;

/**
 * Method Collection Contract
 *
 * This contract represents a collection of methods within PHP structures.
 *
 * @see \Smpl\Inspector\Contracts\Method
 *
 * @extends \Smpl\Inspector\Contracts\Collection<string, \Smpl\Inspector\Contracts\Method>
 */
interface MethodCollection extends Collection
{
    /**
     * Get a method by the provided name.
     *
     * @param string $name
     *
     * @return \Smpl\Inspector\Contracts\Method|null
     */
    public function get(string $name): ?Method;

    /**
     * Check if the collection contains a method by the provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get the method at the provided index.
     *
     * This method is designed to allow retrieval of methods without knowing
     * their name.
     *
     * @param int $index
     *
     * @return \Smpl\Inspector\Contracts\Method|null
     */
    public function indexOf(int $index): ?Method;

    /**
     * Get the first method from this collection.
     *
     * @return \Smpl\Inspector\Contracts\Method|null
     */
    public function first(): ?Method;

    /**
     * Create a new filtered collection using the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\MethodFilter $filter
     *
     * @return static
     */
    public function filter(MethodFilter $filter): static;

    /**
     * Get the names of all the methods contained in this collection.
     *
     * If $includeClass is true, all names will contain the name of the class,
     * otherwise, it will just contain the name of the method.
     *
     * @param bool $includeClass
     *
     * @return string[]
     */
    public function names(bool $includeClass = true): array;

    /**
     * Get an array of methods.
     *
     * @return list<\Smpl\Inspector\Contracts\Method>
     */
    public function values(): array;
}