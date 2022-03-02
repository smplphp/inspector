<?php

namespace Smpl\Inspector\Contracts;

interface PropertyFactory
{
    public function make(Structure $structure): StructurePropertyCollection;
}