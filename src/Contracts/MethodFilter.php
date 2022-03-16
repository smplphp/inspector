<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\Visibility;

interface MethodFilter
{
    public function publicOnly(): static;

    public function protectedOnly(): static;

    public function privateOnly(): static;

    public function hasVisibility(Visibility ...$visibilities): static;

    public function hasReturnType(string|Type|null $type = null): static;

    public function hasNoReturnType(): static;

    public function static(): static;

    public function notStatic(): static;

    public function hasNoParameters(): static;

    public function hasParameters(): static;

    public function parameterCount(int $parameterCount): static;

    public function check(Method $method): bool;
}