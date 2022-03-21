<?php

declare(strict_types=1);

namespace Smpl\Inspector\Contracts;

use Countable;
use IteratorAggregate;

/**
 * @template K of array-key
 * @template V of object
 * @extends IteratorAggregate<K, V>
 */
interface Collection extends IteratorAggregate, Countable
{
    public function isEmpty(): bool;

    public function isNotEmpty(): bool;
}