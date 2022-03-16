<?php

namespace Smpl\Inspector\Contracts;

use IteratorAggregate;
use Countable;

/**
 * @extends IteratorAggregate<string, \Smpl\Inspector\Contracts\Parameter>
 */
interface MethodParameterCollection extends IteratorAggregate, Countable
{
    public function getMethod(): Method;

    public function get(string|int $name): ?Parameter;

    public function has(string|int $name): bool;

    public function filter(ParameterFilter $filter): self;

}