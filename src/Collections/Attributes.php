<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Attribute;
use Smpl\Inspector\Contracts\AttributeCollection;
use Smpl\Inspector\Contracts\MetadataCollection;
use Traversable;

/**
 * @template I of object
 *
 * @implements \Smpl\Inspector\Contracts\AttributeCollection<I>
 */
abstract class Attributes implements AttributeCollection
{
    /**
     * @var \Smpl\Inspector\Contracts\Attribute[]
     */
    private array $attributes;

    /**
     * @var \Smpl\Inspector\Contracts\MetadataCollection<I>[]
     */
    private array $metadata;

    /**
     * @param array<class-string, \Smpl\Inspector\Contracts\Attribute>             $attributes
     * @param array<class-string, \Smpl\Inspector\Contracts\MetadataCollection<I>> $metadata
     */
    public function __construct(array $attributes = [], array $metadata = [])
    {
        $this->attributes = $attributes;
        $this->metadata   = $metadata;
    }

    public function get(string $name): ?Attribute
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param string $name
     *
     * @return \Smpl\Inspector\Contracts\MetadataCollection<I>|null
     */
    public function metadata(string $name): ?MetadataCollection
    {
        return $this->metadata[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return $this->get($name) !== null;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->attributes);
    }

    public function count(): int
    {
        return count($this->attributes);
    }
}