<?php

namespace Smpl\Inspector\Contracts;

/**
 * @template I of object
 */
interface MetadataCollection
{
    public function getAttribute(): Attribute;

    /**
     * @return \Smpl\Inspector\Contracts\Metadata<I>[]
     */
    public function getMetadata(): array;
}