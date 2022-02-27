<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\StructureType as StructureEnum;
use Stringable;

interface Structure extends Stringable
{
    public function getName(): string;

    public function getType(): Type;

    public function getStructureType(): StructureEnum;

    /**
     * @return \Smpl\Inspector\Contracts\Method[]
     */
    public function getMethods(): array;

    /**
     * @return \Smpl\Inspector\Contracts\Property[]
     */
    public function getProperties(): array;

    public function getConstructor(): ?Method;

    public function isInternal(): bool;

    public function isAbstract(): bool;

    public function isInstantiable(): bool;

    public function getParent(): ?Structure;

    /**
     * @return \Smpl\Inspector\Contracts\Structure[]
     */
    public function getInterfaces(): array;
}