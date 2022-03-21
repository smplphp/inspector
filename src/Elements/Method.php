<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionMethod;
use Smpl\Inspector\Concerns\HasAttributes;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodMetadataCollection;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;

class Method implements MethodContract
{
    use HasAttributes;

    private ReflectionMethod          $reflection;
    private Structure                 $structure;
    private ?Type                     $type;
    private Visibility                $visibility;
    private MethodParameterCollection $parameters;
    private MethodMetadataCollection  $metadata;
    private bool                      $hasTrueStructure;
    private Structure                 $trueStructure;

    public function __construct(Structure $structure, ReflectionMethod $reflection, ?Type $type = null)
    {
        $this->structure  = $structure;
        $this->reflection = $reflection;
        $this->type       = $type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getReflection(): ReflectionMethod
    {
        return $this->reflection;
    }

    public function getName(): string
    {
        return $this->reflection->getShortName();
    }

    public function getFullName(): string
    {
        return $this->getStructure()->getFullName() . '::' . $this->getName();
    }

    public function getVisibility(): Visibility
    {
        if (! isset($this->visibility)) {
            $this->visibility = Visibility::getFromReflection($this->getReflection());
        }

        return $this->visibility;
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function isConstructor(): bool
    {
        return $this->getReflection()->isConstructor();
    }

    public function getReturnType(): ?Type
    {
        return $this->type;
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    public function getParameters(): MethodParameterCollection
    {
        if (! isset($this->parameters)) {
            $this->parameters = Inspector::getInstance()->structures()->makeParameters($this);
        }

        return $this->parameters;
    }

    public function getAllMetadata(): MethodMetadataCollection
    {
        if (! isset($this->metadata)) {
            $this->metadata = Inspector::getInstance()->structures()->makeMethodMetadata($this);
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