<?php

declare(strict_types=1);

namespace Smpl\Inspector\Types;

use Smpl\Inspector\Contracts\Type;

class UnionType implements Type
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

    public function __toString()
    {
        return $this->getName();
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

        return ($allowNull && $value === null);
    }

    public function isBuiltin(): bool
    {
        return false;
    }
}