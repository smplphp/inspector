<?php

namespace Smpl\Inspector\Contracts;

use Countable;
use IteratorAggregate;

/**
 * @template I of object
 *
 * @extends IteratorAggregate<int, \Smpl\Inspector\Contracts\Metadata>
 */
interface MetadataCollection extends IteratorAggregate, Countable
{
    public function getAttribute(): Attribute;

    /**
     * @return \Smpl\Inspector\Contracts\Metadata<I>[]
     */
    public function getMetadata(): array;
}