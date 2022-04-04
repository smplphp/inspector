<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Mappers\ComposerMapper;

/**
 * @group inspector
 */
class InspectorTest extends TestCase
{
    /**
     * @test
     */
    public function inspector_defaults_all_dependencies(): void
    {
        $inspector = new Inspector();

        self::assertTrue(true);
    }

    /**
     * @test
     */
    public function inspector_functions_as_a_singleton(): void
    {
        Inspector::setInstance(null);
        self::assertSame(Inspector::getInstance(), Inspector::getInstance());
    }

    /**
     * @test
     */
    public function inspector_lets_you_override_the_singleton_instance(): void
    {
        $inspectorSingleton = Inspector::getInstance();
        $inspector          = new Inspector();

        Inspector::setInstance($inspector);

        self::assertNotSame($inspectorSingleton, Inspector::getInstance());
        self::assertSame($inspector, Inspector::getInstance());
    }

    /**
     * @test
     */
    public function inspector_doesnt_override_the_singleton_instance_when_a_new_one_is_created(): void
    {
        $inspectorSingleton = Inspector::getInstance();
        $inspector          = new Inspector();

        self::assertSame($inspectorSingleton, Inspector::getInstance());
        self::assertNotSame($inspector, Inspector::getInstance());
    }

    /**
     * @test
     */
    public function inspector_defaults_to_using_the_composer_mapper(): void
    {
        $inspector = new Inspector();

        self::assertInstanceOf(ComposerMapper::class, $inspector->getMapper());
    }
}