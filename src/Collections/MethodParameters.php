<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\ParameterFilter;

final class MethodParameters extends Parameters
{
    private Method $method;

    /**
     * @param \Smpl\Inspector\Contracts\Method      $method
     * @param \Smpl\Inspector\Contracts\Parameter[] $parameters
     */
    public function __construct(Method $method, array $parameters)
    {
        $this->method = $method;
        parent::__construct($parameters);
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function filter(ParameterFilter $filter): static
    {
        return new self(
            $this->getMethod(),
            array_filter($this->parameters, $filter->check(...))
        );
    }
}