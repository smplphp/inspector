<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionFunctionAbstract;
use Smpl\Inspector\Contracts\BaseFunction as BaseFunctionContract;
use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\Type;

abstract class BaseFunction implements BaseFunctionContract
{
    private ReflectionFunctionAbstract $reflection;
    private ?Type                      $type;

    public function __construct(ReflectionFunctionAbstract $reflection, ?Type $type = null)
    {
        $this->reflection = $reflection;
        $this->type       = $type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getReflection(): ReflectionFunctionAbstract
    {
        return $this->reflection;
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function getReturnType(): ?Type
    {
        return $this->type;
    }

    public function getParameter(int|string $parameter): ?Parameter
    {
        return is_string($parameter)
            ? $this->getParameters()->get($parameter)
            : $this->getParameters()->indexOf($parameter);
    }

    public function hasParameter(int|string $parameter): bool
    {
        return is_string($parameter)
            ? $this->getParameters()->has($parameter)
            : $this->getParameters()->indexOf($parameter) !== null;
    }
}