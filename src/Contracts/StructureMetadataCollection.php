<?php

namespace Smpl\Inspector\Contracts;

interface StructureMetadataCollection extends MetadataCollection
{
    public function getStructure(): Structure;
}