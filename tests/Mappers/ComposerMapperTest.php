<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Mappers;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Smpl\Inspector\Exceptions\MapperException;
use Smpl\Inspector\Factories\StructureFactory;
use Smpl\Inspector\Factories\TypeFactory;
use Smpl\Inspector\Mappers\ComposerMapper;

/**
 * @group mappers
 * @group composer
 */
class ComposerMapperTest extends TestCase
{
    /**
     * @test
     */
    public function loads_classes_from_map_by_path(): void
    {
        $mapper   = new ComposerMapper();
        $classes1 = $mapper->mapPath('./vendor/psr/container');
        $classes2 = $mapper->mapPath('./src/Factories');

        self::assertCount(3, $classes1);
        self::assertSame(NotFoundExceptionInterface::class, $classes1[0]);
        self::assertSame(ContainerInterface::class, $classes1[1]);
        self::assertSame(ContainerExceptionInterface::class, $classes1[2]);

        self::assertCount(2, $classes2);
        self::assertSame(StructureFactory::class, $classes2[0]);
        self::assertSame(TypeFactory::class, $classes2[1]);
    }

    /**
     * @test
     */
    public function returns_an_empty_array_when_no_classes_are_found(): void
    {
        $mapper  = new ComposerMapper();
        $classes = $mapper->mapPath('./build');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function loads_classes_from_map_by_namespace(): void
    {
        $mapper   = new ComposerMapper();
        $classes1 = $mapper->mapNamespace('Psr\Container');
        $classes2 = $mapper->mapNamespace('Smpl\Inspector\Factories');

        self::assertCount(3, $classes1);
        self::assertSame(NotFoundExceptionInterface::class, $classes1[0]);
        self::assertSame(ContainerInterface::class, $classes1[1]);
        self::assertSame(ContainerExceptionInterface::class, $classes1[2]);

        self::assertCount(2, $classes2);
        self::assertSame(StructureFactory::class, $classes2[0]);
        self::assertSame(TypeFactory::class, $classes2[1]);
    }

    /**
     * @test
     */
    public function returns_an_empty_array_for_namespaces_outside_of_its_map(): void
    {
        $mapper  = new ComposerMapper();
        $classes = $mapper->mapNamespace('IDontExist');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_paths_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'invalid\' is not a valid path for mapping');

        $mapper = new ComposerMapper();
        $mapper->mapPath('invalid');
    }
}