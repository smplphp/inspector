<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Support\AttributeTarget;
use Smpl\Inspector\Tests\Fixtures\AllAttribute;
use Smpl\Inspector\Tests\Fixtures\ClassAttribute;
use Smpl\Inspector\Tests\Fixtures\MethodAttribute;
use Smpl\Inspector\Tests\Fixtures\ParameterAttribute;
use Smpl\Inspector\Tests\Fixtures\PropertyAttribute;

/**
 * @group elements
 * @group attributes
 */
class AttributeTest extends TestCase
{
    private StructureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = StructureFactory::getInstance();
    }

    /**
     * @test
     */
    public function attribute_names_match_their_fqn(): void
    {
        $classAttribute     = $this->factory->makeAttribute(ClassAttribute::class);
        $propertyAttribute  = $this->factory->makeAttribute(PropertyAttribute::class);
        $methodAttribute    = $this->factory->makeAttribute(MethodAttribute::class);
        $parameterAttribute = $this->factory->makeAttribute(ParameterAttribute::class);

        self::assertEquals(ClassAttribute::class, $classAttribute->getName());
        self::assertEquals(PropertyAttribute::class, $propertyAttribute->getName());
        self::assertEquals(MethodAttribute::class, $methodAttribute->getName());
        self::assertEquals(ParameterAttribute::class, $parameterAttribute->getName());
    }

    /**
     * @test
     */
    public function attributes_have_the_correct_repeatable_state(): void
    {
        $classAttribute     = $this->factory->makeAttribute(ClassAttribute::class);
        $propertyAttribute  = $this->factory->makeAttribute(PropertyAttribute::class);
        $methodAttribute    = $this->factory->makeAttribute(MethodAttribute::class);
        $parameterAttribute = $this->factory->makeAttribute(ParameterAttribute::class);

        self::assertTrue($classAttribute->isRepeatable());
        self::assertFalse($propertyAttribute->isRepeatable());
        self::assertFalse($methodAttribute->isRepeatable());
        self::assertFalse($parameterAttribute->isRepeatable());
    }

    /**
     * @test
     */
    public function attributes_have_the_correct_targets(): void
    {
        $classAttribute     = $this->factory->makeAttribute(ClassAttribute::class);
        $propertyAttribute  = $this->factory->makeAttribute(PropertyAttribute::class);
        $methodAttribute    = $this->factory->makeAttribute(MethodAttribute::class);
        $parameterAttribute = $this->factory->makeAttribute(ParameterAttribute::class);
        $allAttribute       = $this->factory->makeAttribute(AllAttribute::class);

        self::assertContains(AttributeTarget::Structure, $classAttribute->getTargets());
        self::assertCount(1, $classAttribute->getTargets());

        self::assertContains(AttributeTarget::Property, $propertyAttribute->getTargets());
        self::assertCount(1, $propertyAttribute->getTargets());

        self::assertContains(AttributeTarget::Method, $methodAttribute->getTargets());
        self::assertCount(1, $methodAttribute->getTargets());

        self::assertContains(AttributeTarget::Parameter, $parameterAttribute->getTargets());
        self::assertCount(1, $parameterAttribute->getTargets());

        self::assertContains(AttributeTarget::Structure, $allAttribute->getTargets());
        self::assertContains(AttributeTarget::Function, $allAttribute->getTargets());
        self::assertContains(AttributeTarget::Method, $allAttribute->getTargets());
        self::assertContains(AttributeTarget::Property, $allAttribute->getTargets());
        self::assertContains(AttributeTarget::Constant, $allAttribute->getTargets());
        self::assertContains(AttributeTarget::Structure, $allAttribute->getTargets());
        self::assertCount(6, $allAttribute->getTargets());

    }
}