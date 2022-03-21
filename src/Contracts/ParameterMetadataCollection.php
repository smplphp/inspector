<?php

namespace Smpl\Inspector\Contracts;

interface ParameterMetadataCollection extends MetadataCollection
{
    public function getParameter(): Parameter;
}