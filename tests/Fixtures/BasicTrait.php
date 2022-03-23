<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

trait BasicTrait
{
    protected function inheritedTraitMethod(): bool
    {
        return true;
    }
}