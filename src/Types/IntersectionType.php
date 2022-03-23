<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class IntersectionType extends BaseType
{
    /**
     * @var \Smpl\Inspector\Contracts\Type[]
     */
    private array $types;

    private string $name;

    public function __construct(Type ...$types)
    {
        $this->types = $types;
        $this->name  = implode(
            '&',
            array_map(static fn(Type $type) => $type->getName(), $this->types)
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function matches(mixed $value): bool
    {
        foreach ($this->types as $type) {
            if (! $type->matches($value)) {
                return false;
            }
        }

        return true;
    }

    public function accepts(Type|string $type): bool
    {
        if ($type === $this->getName() || parent::accepts($type)) {
            return true;
        }

        foreach ($this->types as $baseType) {
            if (! $baseType->accepts($type)) {
                return false;
            }
        }

        return true;
    }

    public function isPrimitive(): bool
    {
        return false;
    }

    public function isBuiltin(): bool
    {
        return false;
    }
}