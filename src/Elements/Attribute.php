<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use Attribute as CoreAttribute;
use Smpl\Inspector\Contracts\Attribute as AttributeContract;
use Smpl\Inspector\Support\AttributeTarget;

class Attribute implements AttributeContract
{
    private CoreAttribute $attribute;
    private string        $name;
    private array         $targets = [];

    public function __construct(CoreAttribute $attribute, string $name)
    {
        $this->attribute = $attribute;
        $this->name      = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRepeatable(): bool
    {
        return (bool)($this->getFlags() & CoreAttribute::IS_REPEATABLE);
    }

    public function getFlags(): int
    {
        return $this->attribute->flags;
    }

    public function getTargets(): array
    {
        if (empty($this->targets)) {
            if ($this->getFlags() === AttributeTarget::All->value) {
                $this->targets = [
                    AttributeTarget::Structure,
                    AttributeTarget::Function,
                    AttributeTarget::Method,
                    AttributeTarget::Property,
                    AttributeTarget::Constant,
                    AttributeTarget::Parameter,
                ];
            } else {
                foreach (AttributeTarget::cases() as $case) {
                    if ($this->getFlags() & $case->value) {
                        $this->targets[] = $case;
                    }
                }
            }
        }

        return $this->targets;
    }

    public function canTarget(AttributeTarget $target): bool
    {
        return in_array($target, $this->getTargets(), true);
    }

    public function __toString()
    {
        return $this->getName();
    }
}