<?php

namespace Smpl\Inspector\Contracts;

interface MethodParameterCollection extends ParameterCollection
{
    public function getMethod(): Method;
}