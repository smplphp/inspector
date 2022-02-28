<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

abstract class BaseType implements Type
{
    /**
     * @codeCoverageIgnore
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function isBuiltin(): bool
    {
        return true;
    }
}