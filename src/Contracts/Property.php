<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\Visibility;

interface Property
{
    public function getStructure(): Structure;

    public function getName(): string;

    public function getType(): ?Type;

    public function getVisibility(): Visibility;

    public function isStatic(): bool;

    public function isNullable(): bool;

    public function hasDefault(): bool;

    public function getDefault(): mixed;
}