<?php

namespace Smpl\Inspector\Contracts;

use Smpl\Inspector\Support\AttributeTarget;

/**
 * @extends \Smpl\Inspector\Contracts\Collection<int, \Smpl\Inspector\Contracts\Metadata>
 */
interface MetadataCollection extends Collection
{
    /**
     * @return \Smpl\Inspector\Contracts\Attribute[]
     */
    public function getAttributes(): array;

    /**
     * @param class-string $attributeName
     *
     * @return \Smpl\Inspector\Contracts\Attribute|null
     */
    public function getAttribute(string $attributeName): ?Attribute;

    /**
     * @param class-string|\Smpl\Inspector\Contracts\Attribute $attribute
     * @param bool                                             $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata[]
     */
    public function get(string|Attribute $attribute, bool $instanceOf = false): array;

    /**
     * @param class-string|\Smpl\Inspector\Contracts\Attribute|null $attribute
     * @param bool                                                  $instanceOf
     *
     * @return \Smpl\Inspector\Contracts\Metadata|null
     */
    public function first(string|Attribute|null $attribute = null, bool $instanceOf = false): ?Metadata;

    /**
     * @param class-string|\Smpl\Inspector\Contracts\Attribute $attribute
     * @param bool                                             $instanceOf
     *
     * @return bool
     */
    public function has(string|Attribute $attribute, bool $instanceOf = false): bool;

    /**
     * @param class-string|\Smpl\Inspector\Contracts\Attribute $attribute
     * @param bool                                             $instanceOf
     *
     * @return int
     */
    public function instances(string|Attribute $attribute, bool $instanceOf = false): int;

    /**
     * @return \Smpl\Inspector\Support\AttributeTarget
     */
    public function getAttributeTarget(): AttributeTarget;
}