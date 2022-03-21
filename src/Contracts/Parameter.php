<?php

declare(strict_types=1);

namespace Smpl\Inspector\Contracts;

use ReflectionParameter;

interface Parameter extends AttributableElement
{
    public function getReflection(): ReflectionParameter;

    public function getMethod(): Method;

    public function getProperty(): ?Property;

    public function getName(): string;

    public function getPosition(): int;

    public function getType(): ?Type;

    public function isNullable(): bool;

    public function hasDefault(): bool;

    public function getDefault(): mixed;

    public function isVariadic(): bool;

    public function isPromoted(): bool;

    public function getAllMetadata(): ParameterMetadataCollection;
}