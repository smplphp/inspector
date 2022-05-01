<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

#[ClassAttribute, ClassAttribute, SecondClassAttribute]
class ExampleClass extends EmptyishClass implements BasicInterface
{
    use BasicTrait;

    #[PropertyAttribute]
    public string $publicStringProperty;

    #[PropertyAttribute]
    private ?int $nullablePrivateIntProperty;

    private ?int $nullablePrivateIntPropertyWithDefault = 3;

    protected mixed $protectedMixedProperty;

    public int|string $publicUnionType;

    public BasicInterface&SecondInterface $publicIntersectionType;

    public function __construct(
        string                    $someString,
        #[ParameterAttribute] int $someNumber = 2,
        public                    readonly bool $promotedPublicBoolProperty = false,
    )
    {
    }

    #[MethodAttribute]
    public function attributedPublicMethodWithoutParameters(): void
    {

    }

    protected function protectedMethodWithAParameter(int $number): int
    {
        return $number;
    }

    private function privateMethodWithMultipleParameters(int $number1, ?int $number2, string $string = 'result'): string
    {
        return $string . ': ' . ($number1 * ($number2 ?? 1));
    }

    public static function staticMethod(): void
    {

    }
}