<?php

namespace Smpl\Inspector\Contracts;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<string, \Smpl\Inspector\Contracts\Method>
 */
interface MethodCollection extends IteratorAggregate, Countable
{
    public function get(string $name): ?Method;

    public function has(string $name): bool;

    public function indexOf(int $index): ?Method;

    public function first(): ?Method;

    public function filter(MethodFilter $filter): self;
}