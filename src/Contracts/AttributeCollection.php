<?php

namespace Smpl\Inspector\Contracts;

use Countable;
use IteratorAggregate;
use Smpl\Inspector\Support\AttributeTarget;

/**
 * @template I of object
 *
 * @extends IteratorAggregate<int, \Smpl\Inspector\Contracts\Attribute>
 */
interface AttributeCollection extends IteratorAggregate, Countable
{
    public function get(string $name): ?Attribute;

    /**
     * @param string $name
     *
     * @return \Smpl\Inspector\Contracts\MetadataCollection<I>|null
     */
    public function metadata(string $name): ?MetadataCollection;

    public function has(string $name): bool;

    public function getAttributeTarget(): AttributeTarget;
}