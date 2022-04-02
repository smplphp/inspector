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
use Smpl\Inspector\Mappers\PSR4Mapper;

/**
 * @group mappers
 * @group psr4
 */
class PSR4MapperTest extends TestCase
{
    /**
     * @var array<string, list<string>>
     */
    private array $map;

    protected function setUp(): void
    {
        $this->map = [
            'Smpl\Inspector' => ['./src'],
            'Psr\Container'  => ['./vendor/psr/container/src'],
        ];
    }

    /**
     * @test
     */
    public function only_loads_classes_from_map_by_path(): void
    {
        $mapper   = new PSR4Mapper($this->map);
        $classes1 = $mapper->mapPath('./vendor/psr');
        $classes2 = $mapper->mapPath('./src/Factories');

        self::assertCount(3, $classes1);
        self::assertContains(NotFoundExceptionInterface::class, $classes1);
        self::assertContains(ContainerInterface::class, $classes1);
        self::assertContains(ContainerExceptionInterface::class, $classes1);

        self::assertCount(2, $classes2);
        self::assertContains(StructureFactory::class, $classes2);
        self::assertContains(TypeFactory::class, $classes2);
    }

    /**
     * @test
     */
    public function returns_an_empty_array_for_paths_outside_of_its_map(): void
    {
        $mapper  = new PSR4Mapper($this->map);
        $classes = $mapper->mapPath('./vendor/composer');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function only_loads_classes_from_map_by_namespace(): void
    {
        $mapper   = new PSR4Mapper($this->map);
        $classes1 = $mapper->mapNamespace('Psr\Container');
        $classes2 = $mapper->mapNamespace('Smpl\Inspector\Factories');

        self::assertCount(3, $classes1);
        self::assertContains(NotFoundExceptionInterface::class, $classes1);
        self::assertContains(ContainerInterface::class, $classes1);
        self::assertContains(ContainerExceptionInterface::class, $classes1);

        self::assertCount(2, $classes2);
        self::assertContains(StructureFactory::class, $classes2);
        self::assertContains(TypeFactory::class, $classes2);
    }

    /**
     * @test
     */
    public function returns_an_empty_array_for_namespaces_outside_of_its_map(): void
    {
        $mapper  = new PSR4Mapper($this->map);
        $classes = $mapper->mapNamespace('Composer');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_paths_in_the_map_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'not-a-path\' is not a valid path for mapping');

        $mapper = new PSR4Mapper(['not-a-namespace' => ['not-a-path']]);
        $mapper->mapPath('./');
    }

    /**
     * @test
     */
    public function throws_an_exception_when_mapping_paths_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'./invalid\' is not a valid path for mapping');

        $mapper = new PSR4Mapper(['not-a-namespace' => ['not-a-path']]);
        $mapper->mapPath('./invalid');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_paths_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'./invalid\' is not a valid path for mapping');

        $mapper = new PSR4Mapper($this->map);
        $mapper->mapPath('./invalid');
    }
}