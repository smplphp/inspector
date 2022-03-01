<?php

declare(strict_types=1);

namespace Smpl\Inspector\Filters;

use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyFilter as PropertyFilterContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;

class PropertyFilter implements PropertyFilterContract
{
    public static function make(): PropertyFilter
    {
        return new self;
    }

    /**
     * @var \Smpl\Inspector\Support\Visibility[]
     */
    protected array       $visibilities = [];
    protected ?bool       $isTyped;
    protected string|Type $hasType;
    protected bool        $isStatic;
    protected bool        $isNullable;
    protected bool        $hasDefaultValue;

    public function publicOnly(): static
    {
        $this->visibilities = [Visibility::Public];
        return $this;
    }

    public function protectedOnly(): static
    {
        $this->visibilities = [Visibility::Protected];
        return $this;
    }

    public function privateOnly(): static
    {
        $this->visibilities = [Visibility::Private];
        return $this;
    }

    public function hasVisibility(Visibility ...$visibilities): static
    {
        $this->visibilities = $visibilities;
        return $this;
    }

    public function typed(): static
    {
        $this->isTyped = true;
        return $this;
    }

    public function notTyped(): static
    {
        $this->isTyped = false;
        return $this;
    }

    public function hasType(Type|string $type): static
    {
        $this->hasType = $type;
        return $this;
    }

    public function static(): static
    {
        $this->isStatic = true;
        return $this;
    }

    public function notStatic(): static
    {
        $this->isStatic = false;
        return $this;
    }

    public function nullable(): static
    {
        $this->isNullable = true;
        return $this;
    }

    public function notNullable(): static
    {
        $this->isNullable = false;
        return $this;
    }

    public function hasDefaultValue(): static
    {
        $this->hasDefaultValue = true;
        return $this;
    }

    public function noDefaultValue(): static
    {
        $this->hasDefaultValue = false;
        return $this;
    }

    public function check(Property $property): bool
    {
        if (! $this->checkVisibility($property)) {
            return false;
        }

        if (! $this->checkTyped($property)) {
            return false;
        }

        if (! $this->checkType($property)) {
            return false;
        }

        if (! $this->checkStatic($property)) {
            return false;
        }

        if (! $this->checkNullable($property)) {
            return false;
        }

        if (! $this->checkDefaultValue($property)) {
            return false;
        }

        return true;
    }

    protected function checkVisibility(Property $property): bool
    {
        if (empty($this->visibilities)) {
            return true;
        }

        return in_array($property->getVisibility(), $this->visibilities, true);
    }

    protected function checkTyped(Property $property): bool
    {
        if (! isset($this->isTyped)) {
            return true;
        }

        return $this->isTyped ? $property->getType() !== null : $property->getType() === null;
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
    protected function checkType(Property $property): bool
    {
        if (! isset($this->hasType)) {
            return true;
        }

        if ($property->getType() === null) {
            return false;
        }

        if (is_string($this->hasType)) {
            $this->hasType = Inspector::getInstance()->types()->make($this->hasType);
        }

        return $this->hasType->accepts($property->getType());
    }

    protected function checkStatic(Property $property): bool
    {
        if (! isset($this->isStatic)) {
            return true;
        }

        return $this->isStatic === $property->isStatic();
    }

    protected function checkNullable(Property $property): bool
    {
        if (! isset($this->isNullable)) {
            return true;
        }

        return $this->isNullable === $property->isNullable();
    }

    protected function checkDefaultValue(Property $property): bool
    {
        if (! isset($this->hasDefaultValue)) {
            return true;
        }

        return $this->hasDefaultValue === $property->hasDefault();
    }
}