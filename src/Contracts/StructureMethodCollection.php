<?php

namespace Smpl\Inspector\Contracts;

interface StructureMethodCollection extends MethodCollection
{
    public function getStructure(): Structure;

    public function countInherited(): int;

    public function countDeclared(): int;
}