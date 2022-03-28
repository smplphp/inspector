<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionMethod;
use Smpl\Inspector\Concerns\HasAttributes;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodMetadataCollection;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\Parameter;
use Smpl\Inspector\Contracts\ParameterFilter;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\StructureFactory;
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
        return $this->getStructure()->getFullName() . Structure::SEPARATOR . $this->getName();
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

    public function getDeclaringStructure(): Structure
    {
        return $this->getStructure();
    }

    public function isInherited(): bool
    {
        return false;
    }

    public function getParameters(?ParameterFilter $filter = null): MethodParameterCollection
    {
        if (! isset($this->parameters)) {
            $this->parameters = StructureFactory::getInstance()->makeMethodParameters($this);
        }

        if ($filter !== null) {
            return $this->parameters->filter($filter);
        }

        return $this->parameters;
    }

    public function getAllMetadata(): MethodMetadataCollection
    {
        if (! isset($this->metadata)) {
            $this->metadata = StructureFactory::getInstance()->makeMethodMetadata($this);
        }

        return $this->metadata;
    }

    public function getParameter(int|string $parameter): ?Parameter
    {
        return is_string($parameter)
            ? $this->getParameters()->get($parameter)
            : $this->getParameters()->indexOf($parameter);
    }

    public function hasParameter(int|string $parameter): bool
    {
        return is_string($parameter)
            ? $this->getParameters()->has($parameter)
            : $this->getParameters()->indexOf($parameter) !== null;
    }
}