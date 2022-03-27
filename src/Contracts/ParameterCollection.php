<?php

namespace Smpl\Inspector\Contracts;

/**
 * Parameter Collection Contract
 *
 * This contract represents a collection of method parameters.
 *
 * @see \Smpl\Inspector\Contracts\Parameter
 * @see \Smpl\Inspector\Contracts\Method
 *
 * @extends \Smpl\Inspector\Contracts\Collection<string, \Smpl\Inspector\Contracts\Parameter>
 */
interface ParameterCollection extends Collection
{
    /**
     * Get a parameter by name.
     *
     * @param string $name
     *
     * @return \Smpl\Inspector\Contracts\Parameter|null
     */
    public function get(string $name): ?Parameter;

    /**
     * Check if this collection has a parameter by the provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get the parameter at the provided position.
     *
     * @param int $position
     *
     * @return \Smpl\Inspector\Contracts\Parameter|null
     */
    public function indexOf(int $position): ?Parameter;

    /**
     * Get the first parameter from this collection.
     *
     * @return \Smpl\Inspector\Contracts\Parameter|null
     */
    public function first(): ?Parameter;

    /**
     * Create a new filtered collection using the provided filter.
     *
     * @param \Smpl\Inspector\Contracts\ParameterFilter $filter
     *
     * @return static
     */
    public function filter(ParameterFilter $filter): static;

    /**
     * Get the names of all the parameters contained in this collection.
     *
     * @return string[]
     */
    public function names(): array;

    /**
     * Get an array of parameters.
     *
     * @return list<\Smpl\Inspector\Contracts\Parameter>
     */
    public function values(): array;

}