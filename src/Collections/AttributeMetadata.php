<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Smpl\Inspector\Contracts\Attribute;
use Smpl\Inspector\Contracts\MetadataCollection as MetadataCollectionContract;
use Traversable;

/**
 * @template I of object
 *
 * @implements \Smpl\Inspector\Contracts\MetadataCollection<I>
 */
final class AttributeMetadata implements MetadataCollectionContract
{
    private Attribute $attribute;
    /**
     * @var \Smpl\Inspector\Contracts\Metadata<I>[]
     */
    private array $metadata;

    /**
     * @param \Smpl\Inspector\Contracts\Attribute     $attribute
     * @param \Smpl\Inspector\Contracts\Metadata<I>[] $metadata
     */
    public function __construct(Attribute $attribute, array $metadata)
    {
        $this->attribute = $attribute;
        $this->metadata  = $metadata;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    /**
     * @return \Smpl\Inspector\Contracts\Metadata<I>[]
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->metadata);
    }

    public function count(): int
    {
        return count($this->metadata);
    }
}