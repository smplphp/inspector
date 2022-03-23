<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

class AttributeException extends InspectorException
{
    public static function baseAttribute(): self
    {
        return new self('Cannot create an attribute instance for PHPs base attribute');
    }

    public static function invalidAttribute(string $attribute): self
    {
        return new self(sprintf(
            'Attribute \'%s\' is not a valid attribute',
            $attribute
        ));
    }

    public static function nonRepeatableAttribute(string $attribute): self
    {
        return new self(sprintf(
            'Attribute \'%s\' is not repeatable, but is provided multiple times',
            $attribute
        ));
    }
}