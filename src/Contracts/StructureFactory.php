<?php

namespace Smpl\Inspector\Contracts;

use ReflectionClass;

interface StructureFactory
{
    /**
     * @param \ReflectionClass|class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    public function make(ReflectionClass|string $class): Structure;
}