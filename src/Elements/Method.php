<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionMethod;
use Smpl\Inspector\Contracts\Method as MethodContract;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\Visibility;
use Smpl\Inspector\Inspector;

class Method implements MethodContract
{
    private static function findVisibility(ReflectionMethod $reflection): Visibility
    {
        if ($reflection->isPrivate()) {
            return Visibility::Private;
        }

        if ($reflection->isProtected()) {
            return Visibility::Protected;
        }

        return Visibility::Public;
    }

    private Structure        $parent;
    private Visibility       $visibility;
    private ReflectionMethod $reflection;
    private Type             $returnType;
    private array            $parameters = [];

    public function __construct(ReflectionMethod $reflection, Structure $parent)
    {
        $this->reflection = $reflection;
        $this->visibility = self::findVisibility($reflection);
        $this->parent     = $parent;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    private function createReturnType(): void
    {
        $this->returnType = Inspector::getInstance()->makeType($this->reflection->getReturnType());
    }

    public function getReturnType(): ?Type
    {
        if ($this->reflection->getReturnType() === null) {
            return null;
        }

        if (! isset($this->returnType)) {
            $this->createReturnType();
        }

        return $this->returnType;
    }

    private function createParameters()
    {
        foreach ($this->reflection->getParameters() as $parameter) {
            $this->parameters[$parameter->getName()] = new Parameter($parameter, $this);
        }
    }

    public function getParameters(): array
    {
        if (empty($this->parameters) && $this->reflection->getNumberOfParameters() > 0) {
            $this->createParameters();
        }

        return $this->parameters;
    }

    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function isConstructor(): bool
    {
        return $this->reflection->isConstructor();
    }

    public function getStructure(): ?Structure
    {
        return $this->parent;
    }

    public function __toString()
    {
        return $this->getName();
    }
}