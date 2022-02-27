<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\Visibility;
use Stringable;

interface Property extends Stringable
{
    public function getType(): ?Type;

    public function getName(): string;

    public function getVisibility(): Visibility;

    public function isStatic(): bool;

    public function isNullable(): bool;

    public function hasDefaultValue(): bool;

    public function getDefaultValue(): mixed;

    public function getStructure(): ?Structure;
}