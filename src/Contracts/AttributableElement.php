<?php

declare(strict_types=1);

namespace Smpl\Inspector\Contracts;

/**
 *
 */
interface AttributableElement
{
    /**
     * @param class-string $attributeClass
     * @param bool         $instanceOf
     *
     * @return bool
     */
    public function hasAttribute(string $attributeClass, bool $instanceOf = false): bool;

    /**
     * @param class-string $attributeClass
     * @param bool         $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata|null
     */
    public function getFirstMetadata(string $attributeClass, bool $instanceOf = false): ?Metadata;

    /**
     * @param class-string $attributeClass
     * @param bool         $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata[]
     */
    public function getMetadata(string $attributeClass, bool $instanceOf = false): array;

    /**
     * @return \Smpl\Inspector\Contracts\Attribute[]
     */
    public function getAttributes(): array;
}