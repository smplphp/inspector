<?php

namespace Smpl\Inspector\Elements;

use ReflectionProperty;
use Smpl\Inspector\Contracts\AttributeCollection;
use Smpl\Inspector\Contracts\Property as PropertyContract;
use Smpl\Inspector\Contracts\PropertyAttributeCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;

class Property implements PropertyContract
{
    private ReflectionProperty          $reflection;
    private Structure                   $structure;
    private ?Type                       $type;
    private Visibility                  $visibility;
    private PropertyAttributeCollection $attributes;

    public function __construct(Structure $structure, ReflectionProperty $reflection, ?Type $type = null)
    {
        $this->structure  = $structure;
        $this->reflection = $reflection;
        $this->type       = $type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getReflection(): ReflectionProperty
    {
        return $this->reflection;
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function getVisibility(): Visibility
    {
        if (! isset($this->visibility)) {
            $this->visibility = Visibility::getFromReflection($this->reflection);
        }

        return $this->visibility;
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function isNullable(): bool
    {
        return $this->reflection->getType()?->allowsNull() ?? true;
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function hasDefault(): bool
    {
        return $this->reflection->hasDefaultValue();
    }

    public function getDefault(): mixed
    {
        return $this->reflection->getDefaultValue();
    }

    public function getAttributes(): PropertyAttributeCollection
    {
        if (! isset($this->attributes)) {
            $this->attributes = Inspector::getInstance()->structures()->makePropertyAttributes($this);
        }

        return $this->attributes;
    }
}