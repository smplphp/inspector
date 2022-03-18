<?php

namespace Smpl\Inspector\Contracts;

interface Attribute
{
    /**
     * @return class-string
     */
    public function getName(): string;

    public function isRepeatable(): bool;

    /**
     * @return \Smpl\Inspector\Support\AttributeTarget[]
     */
    public function getTargets(): array;
}