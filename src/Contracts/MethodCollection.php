<?php

namespace Smpl\Inspector\Contracts;

/**
 * @extends \Smpl\Inspector\Contracts\Collection<string, \Smpl\Inspector\Contracts\Method>
 */
interface MethodCollection extends Collection
{
    public function get(string $name): ?Method;

    public function has(string $name): bool;

    public function indexOf(int $index): ?Method;

    public function first(): ?Method;

    public function filter(MethodFilter $filter): self;

    /**
     * @return string[]
     */
    public function names(): array;
}