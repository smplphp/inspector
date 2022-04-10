<?php

declare(strict_types=1);

namespace Smpl\Inspector\Filters;

use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\Property;
use Smpl\Inspector\Contracts\PropertyFilter as PropertyFilterContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Support\Visibility;

final class PropertyFilter implements PropertyFilterContract
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
    protected string|Type $acceptType;
    protected bool        $accept;
    protected mixed       $matchValue;
    protected bool        $match;
    protected bool        $isStatic;
    protected bool        $isNullable;
    protected bool        $hasDefaultValue;
    /**
     * @var class-string|null
     */
    protected ?string $attribute              = null;
    private bool      $attributeInstanceCheck = false;

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

    public function typeAccepts(Type|string $type): static
    {
        $this->acceptType = $type;
        $this->accept     = true;
        return $this;
    }

    public function typeDoesNotAccept(Type|string $type): static
    {
        $this->acceptType = $type;
        $this->accept     = false;
        return $this;
    }

    public function typeMatches(mixed $value): static
    {
        $this->matchValue = $value;
        $this->match      = true;
        return $this;
    }

    public function typeDoesNotMatch(mixed $value): static
    {
        $this->matchValue = $value;
        $this->match      = false;
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

    /**
     * @param class-string $attribute
     * @param bool         $instanceOf
     *
     * @return static
     */
    public function hasAttribute(string $attribute, bool $instanceOf = false): static
    {
        $this->attribute              = $attribute;
        $this->attributeInstanceCheck = $instanceOf;
        return $this;
    }

    public function check(Property $property): bool
    {
        return $this->checkVisibility($property)
            && $this->checkTyped($property)
            && $this->checkType($property)
            && $this->checkAcceptsType($property)
            && $this->checkMatchesType($property)
            && $this->checkStatic($property)
            && $this->checkNullable($property)
            && $this->checkDefaultValue($property)
            && $this->checkAttribute($property);
    }

    private function checkVisibility(Property $property): bool
    {
        if (empty($this->visibilities)) {
            return true;
        }

        return in_array($property->getVisibility(), $this->visibilities, true);
    }

    private function checkTyped(Property $property): bool
    {
        if (! isset($this->isTyped)) {
            return true;
        }

        return $this->isTyped ? $property->getType() !== null : $property->getType() === null;
    }

    private function checkType(Property $property): bool
    {
        if (! isset($this->hasType)) {
            return true;
        }

        $type = $property->getType();

        if ($type === null) {
            return false;
        }

        if (is_string($this->hasType)) {
            $this->hasType = TypeFactory::getInstance()->make($this->hasType);
        }

        return $type->getName() === $this->hasType->getName();
    }

    private function checkAcceptsType(Property $property): bool
    {
        if (! isset($this->accept)) {
            return true;
        }

        $type = $property->getType();

        if ($type === null) {
            return false;
        }

        return $this->accept === $type->accepts($this->acceptType);
    }

    private function checkMatchesType(Property $property): bool
    {
        if (! isset($this->match)) {
            return true;
        }

        $type = $property->getType();

        if ($type === null) {
            return false;
        }

        return $this->match === $type->matches($this->matchValue);
    }

    private function checkStatic(Property $property): bool
    {
        if (! isset($this->isStatic)) {
            return true;
        }

        return $this->isStatic === $property->isStatic();
    }

    private function checkNullable(Property $property): bool
    {
        if (! isset($this->isNullable)) {
            return true;
        }

        return $this->isNullable === $property->isNullable();
    }

    private function checkDefaultValue(Property $property): bool
    {
        if (! isset($this->hasDefaultValue)) {
            return true;
        }

        return $this->hasDefaultValue === $property->hasDefault();
    }

    private function checkAttribute(Property $property): bool
    {
        if ($this->attribute === null) {
            return true;
        }

        return $property->hasAttribute($this->attribute, $this->attributeInstanceCheck);
    }
}