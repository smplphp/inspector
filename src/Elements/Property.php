<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionProperty;
use Smpl\Inspector\Contracts\Property as PropertyContract;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Support\Visibility;
use Smpl\Inspector\Inspector;

class Property implements PropertyContract
{
    private static function findVisibility(ReflectionProperty $reflection): Visibility
    {
        if ($reflection->isPrivate()) {
            return Visibility::Private;
        }

        if ($reflection->isProtected()) {
            return Visibility::Protected;
        }

        return Visibility::Public;
    }

    private Structure          $parent;
    private Visibility         $visibility;
    private ReflectionProperty $reflection;
    private Type               $type;

    public function __construct(ReflectionProperty $reflection, Structure $parent)
    {
        $this->reflection = $reflection;
        $this->visibility = self::findVisibility($reflection);
        $this->parent     = $parent;
    }

    public function getType(): ?Type
    {
        if (! isset($this->type) && $this->reflection->hasType()) {
            $this->type = Inspector::getInstance()->makeType($this->reflection->getType());
        }

        return $this->type;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function isNullable(): bool
    {
        if ($this->reflection->hasType()) {
            return $this->reflection->getType()?->allowsNull();
        }

        return true;
    }

    public function hasDefaultValue(): bool
    {
        return $this->reflection->hasDefaultValue();
    }

    public function getDefaultValue(): mixed
    {
        return $this->reflection->getDefaultValue();
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