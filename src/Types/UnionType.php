<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class UnionType extends BaseType
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
            '|',
            array_map(fn(Type $type) => $type->getName(), $this->types)
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function matches(mixed $value): bool
    {
        foreach ($this->types as $type) {
            if ($type->matches($value)) {
                return true;
            }
        }

        return false;
    }

    public function accepts(Type|string $type): bool
    {
        foreach ($this->types as $baseType) {
            if ($baseType->accepts($type)) {
                return true;
            }
        }

        return parent::accepts($type);
    }

    public function isPrimitive(): bool
    {
        foreach ($this->types as $type) {
            if ($type->isPrimitive() === false) {
                return false;
            }
        }

        return true;
    }

    public function isBuiltin(): bool
    {
        foreach ($this->types as $type) {
            if ($type->isBuiltin() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return \Smpl\Inspector\Contracts\Type[]
     */
    public function getSubtypes(): array
    {
        return $this->types;
    }
}