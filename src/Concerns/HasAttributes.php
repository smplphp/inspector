<?php

declare(strict_types=1);

namespace Smpl\Inspector\Concerns;

use Smpl\Inspector\Contracts\Metadata;
use Smpl\Inspector\Contracts\MetadataCollection;

/**
 * @psalm-require-implements \Smpl\Inspector\Contracts\AttributableElement
 */
trait HasAttributes
{
    /**
     * @param class-string $attributeClass
     * @param bool         $instanceOf
     *
     * @return bool
     */
    public function hasAttribute(string $attributeClass, bool $instanceOf = false): bool
    {
        return $this->getAllMetadata()->has($attributeClass, $instanceOf);
    }

    /**
     * @return \Smpl\Inspector\Contracts\MetadataCollection
     */
    abstract public function getAllMetadata(): MetadataCollection;

    /**
     * @param class-string $attributeClass
     * @param bool         $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata[]
     */
    public function getMetadata(string $attributeClass, bool $instanceOf = false): array
    {
        return $this->getAllMetadata()->get($attributeClass, $instanceOf);
    }

    /**
     * @param class-string $attributeClass
     * @param bool         $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata|null
     */
    public function getFirstMetadata(string $attributeClass, bool $instanceOf = false): ?Metadata
    {
        return $this->getAllMetadata()->first($attributeClass, $instanceOf);
    }

    /**
     * @return list<\Smpl\Inspector\Contracts\Attribute>
     */
    public function getAttributes(): array
    {
        return $this->getAllMetadata()->getAttributes();
    }
}