<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\AttributeTarget;
use Stringable;

interface Attribute extends Stringable
{
    public function getName(): string;

    public function isRepeatable(): bool;

    public function getFlags(): int;

    /**
     * @return array<AttributeTarget>
     */
    public function getTargets(): array;

    public function canTarget(AttributeTarget $target): bool;
}