<?php

namespace Smpl\Inspector\Contracts;

interface StructurePropertyCollection extends PropertyCollection
{
    public function getStructure(): Structure;
}