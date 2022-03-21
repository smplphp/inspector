<?php

namespace Smpl\Inspector\Elements;

use ReflectionProperty;
use Smpl\Inspector\Concerns\HasAttributes;
use Smpl\Inspector\Contracts\Property as PropertyContract;
use Smpl\Inspector\Contracts\PropertyMetadataCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;

class Property implements PropertyContract
{
    use HasAttributes;

    private ReflectionProperty         $reflection;
    private Structure                  $structure;
    private ?Type                      $type;
    private Visibility                 $visibility;
    private PropertyMetadataCollection $metadata;
    private bool                       $hasTrueStructure;
    private Structure                  $trueStructure;

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

    public function getAllMetadata(): PropertyMetadataCollection
    {
        if (! isset($this->metadata)) {
            $this->metadata = Inspector::getInstance()->structures()->makePropertyMetadata($this);
        }

        return $this->metadata;
    }

    public function getDeclaringStructure(): Structure
    {
        if (! isset($this->hasTrueStructure)) {
            $this->hasTrueStructure = $this->getStructure()->getFullName() === $this->getReflection()->class;

            if ($this->hasTrueStructure) {
                $this->trueStructure = Inspector::getInstance()->structures()->makeStructure($this->getReflection()->getDeclaringClass());
            }
        }

        return $this->hasTrueStructure ? $this->trueStructure : $this->getStructure();
    }

    public function isInherited(): bool
    {
        return $this->getDeclaringStructure()->getName() === $this->getStructure()->getName();
    }
}