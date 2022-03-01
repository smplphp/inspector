<?php

namespace Smpl\Inspector\Contracts;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<string, \Smpl\Inspector\Contracts\Property>
 */
interface StructurePropertyCollection extends IteratorAggregate, Countable
{
    public function getStructure(): Structure;

    public function get(string $name): ?Property;

    public function has(string $name): bool;

    public function filter(PropertyFilter $filter): self;
}