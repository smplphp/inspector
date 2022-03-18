<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

#[TestAttribute, TestAttribute]
class ClassWithAttributes
{
    #[TestAttribute]
    private int $property;

    private string $noAttributeProperty;

    #[MethodAttribute]
    public function __construct(#[TestAttribute] int $parameter, string $noAttributeParameter)
    {
    }

    public function noAttributeMethod()
    {

    }
}