<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Method;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\ParameterCollection;

final class MethodParameters extends Parameters implements MethodParameterCollection
{
    public static function for(Method $method, ParameterCollection $parameters): self
    {
        return new self($method, $parameters->values());
    }

    private Method $method;

    /**
     * @param \Smpl\Inspector\Contracts\Method          $method
     * @param list<\Smpl\Inspector\Contracts\Parameter> $parameters
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
}