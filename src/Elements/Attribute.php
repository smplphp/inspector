<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use Attribute as BaseAttribute;
use Smpl\Inspector\Contracts\Attribute as AttributeContract;
use Smpl\Inspector\Support\AttributeTarget;

/**
 * @psalm-suppress MixedOperand
 * @psalm-suppress MixedArgument
 */
class Attribute implements AttributeContract
{
    private BaseAttribute $attribute;

    /**
     * @var class-string
     */
    private string $class;

    /**
     * @var \Smpl\Inspector\Support\AttributeTarget[]
     */
    private array $targets;

    /**
     * @param class-string $class
     * @param \Attribute   $attribute
     */
    public function __construct(string $class, BaseAttribute $attribute)
    {
        $this->class     = $class;
        $this->attribute = $attribute;
    }

    public function getName(): string
    {
        return $this->class;
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    private function getFlags(): int
    {
        return $this->attribute->flags;
    }


    public function isRepeatable(): bool
    {
        return (bool)($this->getFlags() & BaseAttribute::IS_REPEATABLE);
    }

    public function getTargets(): array
    {
        if (! isset($this->targets)) {
            $this->targets = AttributeTarget::for($this->getFlags());
        }

        return $this->targets;
    }

    /**
     * @codeCoverageIgnore
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}