<?php

declare(strict_types=1);

namespace Smpl\Inspector\Filters;

use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\MethodFilter as MethodFilterContract;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;

class MethodFilter implements MethodFilterContract
{
    public static function make(): MethodFilter
    {
        return new self;
    }

    /**
     * @var \Smpl\Inspector\Support\Visibility[]
     */
    protected array       $visibilities   = [];
    protected ?bool       $isTyped;
    protected string|Type $hasReturnType;
    protected bool        $isStatic;
    protected bool        $isNullable;
    protected bool        $hasDefaultValue;
    protected ?int        $parameterCount = null;
    protected ?bool       $hasParameters  = null;

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

    public function hasReturnType(Type|string|null $type = null): static
    {
        if ($type !== null) {
            $this->hasReturnType = $type;
        } else {
            $this->isTyped = true;
        }

        return $this;
    }

    public function hasNoReturnType(): static
    {
        $this->isTyped = false;
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

    public function hasNoParameters(): static
    {
        $this->hasParameters = false;
        return $this;
    }

    public function hasParameters(): static
    {
        $this->hasParameters = true;
        return $this;
    }

    public function parameterCount(int $parameterCount): static
    {
        $this->parameterCount = $parameterCount;
        return $this;
    }

    public function check(Method $method): bool
    {
        return $this->checkVisibility($method)
            && $this->checkTyped($method)
            && $this->checkType($method)
            && $this->checkStatic($method)
            && $this->checkParameters($method)
            && $this->checkParameterCount($method);
    }

    protected function checkVisibility(Method $method): bool
    {
        if (empty($this->visibilities)) {
            return true;
        }

        return in_array($method->getVisibility(), $this->visibilities, true);
    }

    protected function checkTyped(Method $method): bool
    {
        if (! isset($this->isTyped)) {
            return true;
        }

        return $this->isTyped === ($method->getReturnType() !== null);
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
    protected function checkType(Method $method): bool
    {
        if (! isset($this->hasReturnType)) {
            return true;
        }

        if ($method->getReturnType() === null) {
            return false;
        }

        if (is_string($this->hasReturnType)) {
            $this->hasReturnType = Inspector::getInstance()->types()->make($this->hasReturnType);
        }

        return $this->hasReturnType->accepts($method->getReturnType());
    }

    protected function checkStatic(Method $method): bool
    {
        if (! isset($this->isStatic)) {
            return true;
        }

        return $this->isStatic === $method->isStatic();
    }

    protected function checkParameters(Method $method): bool
    {
        if ($this->hasParameters === null) {
            return true;
        }

        return $this->hasParameters === $method->getParameters()->count() > 0;
    }

    protected function checkParameterCount(Method $method): bool
    {
        if ($this->parameterCount === null) {
            return true;
        }

        return $method->getParameters()->count() === $this->parameterCount;
    }
}