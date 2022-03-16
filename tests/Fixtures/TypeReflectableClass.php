<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Types\StringType;

abstract class TypeReflectableClass
{
    public array $array = [];

    public bool $bool;

    public Type $class;

    public float $float;

    public int $int;

    public static iterable $iterable;

    public mixed $mixed;

    public object $object;

    public string $string;

    public ?string $nullable;

    public string|int $union;

    public Type&StringType $intersection;

    private $noType;

    public function voidReturn(): void
    {
    }

    private function privateMethod()
    {

    }

    protected function protectedMethod(): void
    {

    }

    public function publicMethodInt(): int
    {
        return 0;
    }

    protected function protectedMethodString(): string
    {
        return 'string';
    }

    public function publicMethodWithParameter(int $param = 1): float|int
    {
        return $param * 2;
    }

    public static function publicMethodWithParameters(int $param1, int $param2, int $param3): float|int
    {
        return $param1 + $param2 + $param3;
    }

    abstract protected function anAbstractFunction(): void;
}