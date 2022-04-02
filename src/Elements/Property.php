<?php

namespace Smpl\Inspector\Elements;

use ReflectionProperty;
use Smpl\Inspector\Concerns\HasAttributes;
use Smpl\Inspector\Contracts\Property as PropertyContract;
use Smpl\Inspector\Contracts\PropertyMetadataCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Support\Visibility;

class Property implements PropertyContract
{
    use HasAttributes;

    private ReflectionProperty         $reflection;
    private Structure                  $structure;
    private ?Type                      $type;
    private Visibility                 $visibility;
    private PropertyMetadataCollection $metadata;

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

    public function getDeclaringStructure(): Structure
    {
        return $this->getStructure();
    }

    public function isInherited(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getFullName(): string
    {
        return $this->getStructure()->getFullName() . Structure::SEPARATOR . $this->getName();
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

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function isPromoted(): bool
    {
        return $this->getReflection()->isPromoted();
    }

    public function getAllMetadata(): PropertyMetadataCollection
    {
        if (! isset($this->metadata)) {
            $this->metadata = StructureFactory::getInstance()->makePropertyMetadata($this);
        }

        return $this->metadata;
    }
}