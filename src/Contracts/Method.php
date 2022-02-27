<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\Visibility;
use Stringable;

interface Method extends Stringable
{
    public function getName(): string;

    public function getReturnType(): ?Type;

    /**
     * @return array<int, \Smpl\Inspector\Contracts\Parameter>
     */
    public function getParameters(): array;

    public function getVisibility(): Visibility;

    public function isStatic(): bool;

    public function isConstructor(): bool;

    public function getStructure(): ?Structure;
}