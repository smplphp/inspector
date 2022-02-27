<?php

namespace Smpl\Inspector\Contracts;

interface HasMetadata
{
    public function getAllMetadata(): array;

    public function getMetadata(string|Attribute $attribute): array;

    public function hasMetadata(string|Attribute $attribute): bool;
}