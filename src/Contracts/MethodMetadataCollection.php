<?php

namespace Smpl\Inspector\Contracts;

interface MethodMetadataCollection extends MetadataCollection
{
    public function getMethod(): Method;
}