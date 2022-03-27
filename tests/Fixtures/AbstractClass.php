<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

abstract class AbstractClass
{
    public function __construct()
    {
    }

    public function nonAbstractMethod(): void
    {

    }

    abstract public function abstractMethod(): void;

    public static function staticMethod(): int
    {
        return 1;
    }

    public function noReturnType()
    {

    }

    public function boolReturnType(bool $bool): bool
    {
        return $bool;
    }
}