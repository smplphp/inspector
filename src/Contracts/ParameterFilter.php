<?php

namespace Smpl\Inspector\Contracts;

interface ParameterFilter
{
    public function typed(): static;

    public function notTyped(): static;

    public function promoted(): static;

    public function notPromoted(): static;

    public function variadic(): static;

    public function notVariadic(): static;

    public function hasType(string|Type $type): static;

    public function nullable(): static;

    public function notNullable(): static;

    public function hasDefaultValue(): static;

    public function noDefaultValue(): static;

    public function check(Parameter $parameter): bool;
}