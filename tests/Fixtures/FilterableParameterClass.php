<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

class FilterableParameterClass
{
    public function __construct(
        $noType,
        string $stringType,
        ?bool $nullableType = false,
        ?int $defaultType = 0,
        #[ParameterAttribute] $attributeParameter = null,
        public readonly int $promotedParameter = 3
    )
    {
    }

    public function noParameters(): void
    {

    }

    public function variadicParameters(string $name, float $aNumber, int ...$numbers): string
    {
        return $name . ': ' . ($aNumber * array_sum($numbers));
    }
}