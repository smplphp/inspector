<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Types\StringType;

class TypeReflectableClass
{
    public array $array;

    public bool $bool;

    public Type $class;

    public float $float;

    public int $int;

    public iterable $iterable;

    public mixed $mixed;

    public object $object;

    public string $string;

    public ?string $nullable;

    public string|int $union;

    public Type&StringType $intersection;

    public $noType;

    public function voidReturn(): void
    {
    }
}