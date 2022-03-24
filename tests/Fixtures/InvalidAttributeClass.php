<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

class InvalidAttributeClass
{
    #[PropertyAttribute, PropertyAttribute]
    public string $invalidAttributeProperty;
}