<?php

namespace Smpl\Inspector\Contracts;

use Stringable;

interface Parameter extends Stringable
{
    public function getType(): ?Type;

    public function getName(): string;

    public function getPosition(): int;

    public function isNullable(): bool;

    public function hasDefaultValue(): bool;

    public function getDefaultValue(): mixed;

    public function getMethod(): ?Method;
}