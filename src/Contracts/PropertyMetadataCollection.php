<?php

namespace Smpl\Inspector\Contracts;

interface PropertyMetadataCollection extends MetadataCollection
{
    public function getProperty(): Property;
}