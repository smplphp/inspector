<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\Visibility;

interface PropertyFilter
{
    public function publicOnly(): static;

    public function protectedOnly(): static;

    public function privateOnly(): static;

    public function hasVisibility(Visibility ...$visibilities): static;

    public function typed(): static;

    public function notTyped(): static;

    public function hasType(string|Type $type): static;

    public function static(): static;

    public function notStatic(): static;

    public function nullable(): static;

    public function notNullable(): static;

    public function hasDefaultValue(): static;

    public function noDefaultValue(): static;

    /**
     * @param class-string $attribute
     * @param bool         $instanceOf
     *
     * @return static
     */
    public function hasAttribute(string $attribute, bool $instanceOf = false): static;

    public function check(Property $property): bool;
}