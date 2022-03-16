<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionMethod;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\MethodParameterCollection;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\Visibility;

class Method implements MethodContract
{
    private ReflectionMethod          $reflection;
    private Structure                 $structure;
    private ?Type                     $type;
    private Visibility                $visibility;
    private MethodParameterCollection $parameters;

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
}