<?php

namespace Smpl\Inspector\Contracts;

/**
 * @extends \Smpl\Inspector\Contracts\Collection<string, \Smpl\Inspector\Contracts\Property>
 */
interface PropertyCollection extends Collection
{
    public function get(string $name): ?Property;

    public function has(string $name): bool;

    public function filter(PropertyFilter $filter): self;
}