<?php

declare(strict_types=1);

namespace Smpl\Inspector\Filters;

use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\ParameterFilter as ParameterFilterContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;

class ParameterFilter implements ParameterFilterContract
{
    public static function make(): ParameterFilter
    {
        return new self;
    }

    protected ?bool       $isTyped;
    protected string|Type $hasType;
    protected ?bool       $isPromoted;
    protected ?bool       $isVariadic;
    protected bool        $isNullable;
    protected bool        $hasDefaultValue;

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

    public function promoted(): static
    {
        $this->isPromoted = true;
        return $this;
    }

    public function notPromoted(): static
    {
        $this->isPromoted = false;
        return $this;
    }

    public function variadic(): static
    {
        $this->isVariadic = true;
        return $this;
    }

    public function notVariadic(): static
    {
        $this->isVariadic = false;
        return $this;
    }

    public function check(Parameter $parameter): bool
    {
        return $this->checkTyped($parameter)
            && $this->checkType($parameter)
            && $this->checkNullable($parameter)
            && $this->checkDefaultValue($parameter)
            && $this->checkPromoted($parameter)
            && $this->checkVariadic($parameter);
    }

    protected function checkTyped(Parameter $parameter): bool
    {
        if (! isset($this->isTyped)) {
            return true;
        }

        return $this->isTyped ? $parameter->getType() !== null : $parameter->getType() === null;
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
    protected function checkType(Parameter $parameter): bool
    {
        if (! isset($this->hasType)) {
            return true;
        }

        if ($parameter->getType() === null) {
            return false;
        }

        if (is_string($this->hasType)) {
            $this->hasType = Inspector::getInstance()->types()->make($this->hasType);
        }

        return $this->hasType->accepts($parameter->getType());
    }

    protected function checkNullable(Parameter $parameter): bool
    {
        if (! isset($this->isNullable)) {
            return true;
        }

        return $this->isNullable === $parameter->isNullable();
    }

    protected function checkDefaultValue(Parameter $parameter): bool
    {
        if (! isset($this->hasDefaultValue)) {
            return true;
        }

        return $this->hasDefaultValue === $parameter->hasDefault();
    }

    private function checkPromoted(Parameter $parameter): bool
    {
        if (! isset($this->isPromoted)) {
            return true;
        }

        return $this->isPromoted === $parameter->isPromoted();
    }

    private function checkVariadic(Parameter $parameter): bool
    {
        if (! isset($this->isVariadic)) {
            return true;
        }

        return $this->isVariadic === $parameter->isVariadic();
    }
}