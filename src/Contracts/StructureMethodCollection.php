<?php

namespace Smpl\Inspector\Contracts;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<string, \Smpl\Inspector\Contracts\Method>
 */
interface StructureMethodCollection extends IteratorAggregate, Countable
{
    public function getStructure(): Structure;

    public function get(string $name): ?Method;

    public function has(string $name): bool;

    public function filter(MethodFilter $filter): self;
}