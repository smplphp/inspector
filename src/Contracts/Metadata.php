<?php

namespace Smpl\Inspector\Contracts;

use Stringable;

interface Metadata extends Stringable
{
    public function getName(): string;

    public function getAttribute(): Attribute;

    /**
     * @return mixed[]
     */
    public function getArguments(): array;

    public function getInstance(): mixed;

    public function getStructure(): ?Structure;
}