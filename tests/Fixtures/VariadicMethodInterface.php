<?php

namespace Smpl\Inspector\Tests\Fixtures;

interface VariadicMethodInterface
{
    public function testFunction(int $number, string ...$variadic);
}