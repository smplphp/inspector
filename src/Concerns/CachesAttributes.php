<?php

declare(strict_types=1);

namespace Smpl\Inspector\Concerns;

use Smpl\Inspector\Contracts\Attribute;

trait CachesAttributes
{
    /**
     * @var array<class-string, \Smpl\Inspector\Contracts\Attribute>
     */
    private array $attributes = [];

    /**
     * Get an already created class attribute.
     *
     * @param class-string $class
     *
     * @return \Smpl\Inspector\Contracts\Attribute|null
     */
    protected function getAttribute(string $class): ?Attribute
    {
        return $this->attributes[$class] ?? null;
    }

    /**
     * Check if a class attribute has already been created.
     *
     * @param class-string $class
     *
     * @return bool
     */
    protected function hasAttribute(string $class): bool
    {
        return $this->getAttribute($class) !== null;
    }

    /**
     * Cache a created attribute and return it.
     *
     * @param \Smpl\Inspector\Contracts\Attribute $attribute
     *
     * @return \Smpl\Inspector\Contracts\Attribute
     */
    protected function addAttribute(Attribute $attribute): Attribute
    {
        $this->attributes[$attribute->getName()] = $attribute;
        return $attribute;
    }
}