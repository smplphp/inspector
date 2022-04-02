<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

class EmptyishClass
{
    protected string $iAmAnInheritedProperty;

    protected function iAmInherited(): void
    {
    }

    public static $mixed;
}