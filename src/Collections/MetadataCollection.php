<?php

declare(strict_types=1);

namespace Smpl\Inspector\Collections;

use ArrayIterator;
use Closure;
use Smpl\Inspector\Contracts\Attribute;
use Smpl\Inspector\Contracts\Metadata;
use Smpl\Inspector\Contracts\MetadataCollection as MetadataCollectionContract;
use Traversable;

abstract class MetadataCollection implements MetadataCollectionContract
{
    /**
     * Get the name of an attribute.
     *
     * This method returns the name from the attribute instance, or the value
     * provided if it's a string.
     *
     * @param \Smpl\Inspector\Contracts\Attribute|class-string $attribute
     *
     * @return class-string
     */
    private static function getAttributeName(Attribute|string $attribute): string
    {
        return $attribute instanceof Attribute ? $attribute->getName() : $attribute;
    }

    /**
     * Make an array of attributes from an array of metadata.
     *
     * @param \Smpl\Inspector\Contracts\Metadata[] $metadata
     *
     * @return list<\Smpl\Inspector\Contracts\Attribute>
     */
    private static function makeAttributes(array $metadata): array
    {
        $attributes = [];
        $used       = [];

        foreach ($metadata as $metadatum) {
            $attribute = $metadatum->getAttribute();

            if (isset($used[$attribute->getName()])) {
                continue;
            }

            $used[$attribute->getName()] = true;
            $attributes[]                = $attribute;

        }

        return $attributes;
    }

    /**
     * Get a filter for performing a comparison of metadata attribute.
     *
     * @param class-string $attribute
     *
     * @return \Closure
     */
    private static function noneInstanceOfFilter(string $attribute): Closure
    {
        return static fn(Metadata $metadata) => $metadata->getAttribute()->getName() === $attribute;
    }

    /**
     * Get a filter for performing an instanceof check on a metadata attribute.
     *
     * @param class-string $attribute
     *
     * @return \Closure
     */
    private static function instanceOfFilter(string $attribute): Closure
    {
        return static fn(Metadata $metadata) => is_subclass_of($metadata->getAttribute()->getName(), $attribute);
    }

    /**
     * @var list<\Smpl\Inspector\Contracts\Attribute>
     */
    private array $attributes;

    /**
     * @var \Smpl\Inspector\Contracts\Metadata[]
     */
    private array $metadata;

    /**
     * @param list<\Smpl\Inspector\Contracts\Metadata> $metadata
     */
    public function __construct(array $metadata)
    {
        $this->metadata   = $metadata;
        $this->attributes = self::makeAttributes($this->metadata);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->metadata);
    }

    public function count(): int
    {
        return count($this->metadata);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $attributeName): ?Attribute
    {
        return array_filter(
                   $this->attributes,
                   static fn(Attribute $attribute) => $attribute->getName() === $attributeName
               )[0] ?? null;
    }

    public function get(Attribute|string $attribute, bool $instanceOf = false): array
    {
        $attribute = self::getAttributeName($attribute);

        return array_filter(
            $this->metadata,
            $instanceOf ? self::instanceOfFilter($attribute) : self::noneInstanceOfFilter($attribute)
        );
    }

    public function first(Attribute|string|null $attribute = null, bool $instanceOf = false): ?Metadata
    {
        if ($attribute === null) {
            return $this->metadata[0];
        }

        return $this->get($attribute, $instanceOf)[0] ?? null;
    }

    public function has(Attribute|string $attribute, bool $instanceOf = false): bool
    {
        return ! empty($this->get($attribute, $instanceOf));
    }

    public function countInstances(Attribute|string $attribute, bool $instanceOf = false): int
    {
        return count($this->get($attribute, $instanceOf));
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
}