<?php

namespace Smpl\Inspector\Contracts;

use IteratorAggregate;
use Countable;

/**
 * @extends IteratorAggregate<string, \Smpl\Inspector\Contracts\Parameter>
 */
interface ParameterCollection extends IteratorAggregate, Countable
{
    public function get(string|int $name): ?Parameter;

    public function has(string|int $name): bool;

    public function filter(ParameterFilter $filter): self;

}