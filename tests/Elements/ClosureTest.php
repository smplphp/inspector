<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Filters\ParameterFilter;
use Smpl\Inspector\Tests\Fixtures\FunctionAttribute;
use Smpl\Inspector\Types\BoolType;

/**
 * @group elements
 * @group closures
 */
class ClosureTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = StructureFactory::getInstance();
    }

    /**
     * @test
     */
    public function closures_know_if_they_are_static(): void
    {
        $staticClosure    = $this->factory->makeClosure(static fn() => true);
        $nonStaticClosure = $this->factory->makeClosure(fn() => true);

        self::assertTrue($staticClosure->isStatic());
        self::assertFalse($nonStaticClosure->isStatic());
    }

    /**
     * @test
     */
    public function closures_know_their_return_type(): void
    {
        $noReturnType  = $this->factory->makeClosure(fn() => false);
        $hasReturnType = $this->factory->makeClosure(fn(): bool => false);

        self::assertNull($noReturnType->getReturnType());
        self::assertNotNull($hasReturnType->getReturnType());
        self::assertInstanceOf(BoolType::class, $hasReturnType->getReturnType());
    }

    /**
     * @test
     */
    public function closures_have_a_collection_of_parameters(): void
    {
        $noParameters    = $this->factory->makeClosure(fn() => true)->getParameters();
        $oneParameter    = $this->factory->makeClosure(fn(int $i) => $i)->getParameters();
        $threeParameters = $this->factory->makeClosure(fn(int $x, int $y, int $z) => false)->getParameters();

        self::assertCount(0, $noParameters);
        self::assertCount(1, $oneParameter);
        self::assertCount(3, $threeParameters);
    }

    /**
     * @test
     */
    public function closures_can_filter_their_collection_of_parameters(): void
    {
        $closure       = $this->factory->makeClosure(fn(int $x, int $y, int $z, string $d) => false);
        $allParameters = $closure->getParameters();
        $parameters    = $closure->getParameters(ParameterFilter::make()->hasType('int'));

        self::assertCount(4, $allParameters);
        self::assertCount(3, $parameters);
    }

    /**
     * @test
     */
    public function closures_can_check_if_they_have_a_single_parameter(): void
    {
        $closure = $this->factory->makeClosure(fn(int $x, int $y, int $z, string $d) => false);

        self::assertTrue($closure->hasParameter('x'));
    }

    /**
     * @test
     */
    public function closures_can_retrieve_a_single_parameter(): void
    {
        $closure   = $this->factory->makeClosure(fn(int $x, int $y, int $z, string $d) => false);
        $parameter = $closure->getParameter('z');

        self::assertNotNull($parameter);
    }

    /**
     * @test
     */
    public function closures_can_check_if_they_have_a_single_parameter_by_index(): void
    {
        $closure = $this->factory->makeClosure(fn(int $x, int $y, int $z, string $d) => false);

        self::assertTrue($closure->hasParameter(3));
    }

    /**
     * @test
     */
    public function closures_can_retrieve_a_single_parameter_by_index(): void
    {
        $closure   = $this->factory->makeClosure(fn(int $x, int $y, int $z, string $d) => false);
        $parameter = $closure->getParameter(3);

        self::assertNotNull($parameter);
        self::assertSame('d', $parameter->getName());
    }
}