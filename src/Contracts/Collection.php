<?php

declare(strict_types=1);

namespace Smpl\Inspector\Contracts;

use Countable;
use IteratorAggregate;

/**
 * Collection Contract
 *
 * This contract is designed to be used with other contracts, as base for all
 * the different types of collections available.
 *
 * @template K of array-key
 * @template V of object
 * @extends IteratorAggregate<K, V>
 */
interface Collection extends IteratorAggregate, Countable
{
    /**
     * Check if the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Check if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool;
}