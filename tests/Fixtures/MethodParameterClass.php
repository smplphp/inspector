<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

class MethodParameterClass
{
    public function __construct(
        public string $promoted,
        int           $notPromoted
    )
    {
    }

    public function someParameters($noType, int $number, string $name, ?bool $boolean = null, string ...$variadicStrings)
    {

    }
}