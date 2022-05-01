<?php
/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use Smpl\Inspector\Contracts\Closure;
use Smpl\Inspector\Contracts\ClosureParameterCollection;
use Smpl\Inspector\Contracts\ParameterCollection;

final class ClosureParameters extends Parameters implements ClosureParameterCollection
{
    public static function for(Closure $closure, ParameterCollection $parameters): self
    {
        return new self($closure, $parameters->values());
    }

    private Closure $closure;

    /**
     * @param \Smpl\Inspector\Contracts\Closure         $closure
     * @param list<\Smpl\Inspector\Contracts\Parameter> $parameters
     */
    public function __construct(Closure $closure, array $parameters)
    {
        $this->closure = $closure;
        parent::__construct($parameters);
    }

    public function getClosure(): Closure
    {
        return $this->closure;
    }

    public function asBase(): ParameterCollection
    {
        return new Parameters($this->values());
    }
}