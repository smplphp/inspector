<?php

declare(strict_types=1);

namespace Smpl\Inspector\Concerns;

use Smpl\Inspector\Contracts\Structure;

trait CollectionForStructure
{
    protected function normaliseKey(string $key): string
    {
        if (! str_contains($key, Structure::SEPARATOR)) {
            $key = $this->getStructure()->getFullName() . Structure::SEPARATOR . $key;
        }

        return $key;
    }
}