<?php

namespace Smpl\Inspector\Contracts;

use Stringable;

interface Type extends Stringable
{
    public function getName(): string;

    public function matches(mixed $value): bool;

    public function isBuiltin(): bool;
}