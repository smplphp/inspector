<?php

declare(strict_types=1);

namespace Smpl\Inspector\Exceptions;

class AttributeException extends InspectorException
{
    public static function invalidAttribute(string $attribute): static
    {
        return new static(sprintf(
            'Attribute \'%s\' is not a valid attribute',
            $attribute
        ));
    }

    public static function nonRepeatableAttribute(string $attribute): static
    {
        return new static(sprintf(
            'Attribute \'%s\' is not repeatable, but is provided multiple times',
            $attribute
        ));
    }
}