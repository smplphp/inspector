<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

#[ClassAttribute, SecondClassAttribute, SecondClassAttribute]
class InvalidAttributeClass
{
    #[PropertyAttribute, PropertyAttribute]
    public string $invalidAttributeProperty;

    #[MethodAttribute]
    public int $anotherInvalidAttributeProperty;

    #[MethodAttribute, MethodAttribute]
    public function invalidAttributeMethod()
    {

    }

    public function anotherMethod(
        #[ParameterAttribute, ParameterAttribute] $uhoh
    )
    {

    }
}