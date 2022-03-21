<?php

namespace Smpl\Inspector\Contracts;

/**
 * @extends \Smpl\Inspector\Contracts\Collection<string, \Smpl\Inspector\Contracts\Parameter>
 */
interface ParameterCollection extends Collection
{
    public function get(string|int $name): ?Parameter;

    public function has(string|int $name): bool;

    public function filter(ParameterFilter $filter): self;

}