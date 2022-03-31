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
use Smpl\Inspector\Mappers\ClassmapMapper;
use Smpl\Inspector\Mappers\MultiMapper;
use Smpl\Inspector\Mappers\PSR4Mapper;

/**
 * @group mappers
 * @group multi
 */
class MultiMapperTest extends TestCase
{
    /**
     * @var array<string, list<string>>
     */
    private array $map1;

    /**
     * @var array<string, list<string>>
     */
    private array $map2;

    protected function setUp(): void
    {
        $this->map1 = [
            'LSS\Array2XML' => './vendor/openlss/lib-array2xml/LSS/Array2XML.php',
            'LSS\XML2Array' => './vendor/openlss/lib-array2xml/LSS/XML2Array.php',
        ];
        $this->map2 = [
            'Smpl\Inspector' => ['./src'],
            'Psr\Container'  => ['./vendor/psr/container/src'],
        ];
    }

    /**
     * @test
     */
    public function only_loads_classes_from_map_by_path(): void
    {
        $mapper   = new MultiMapper([
            new ClassmapMapper($this->map1),
            new PSR4Mapper($this->map2),
        ]);
        $classes1 = $mapper->mapPath('./vendor/psr');
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
    public function returns_an_empty_array_for_paths_outside_of_its_map(): void
    {
        $mapper  = new MultiMapper([
            new ClassmapMapper($this->map1),
            new PSR4Mapper($this->map2),
        ]);
        $classes = $mapper->mapPath('./vendor/composer');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function only_loads_classes_from_map_by_namespace(): void
    {
        $mapper   = new MultiMapper([
            new ClassmapMapper($this->map1),
            new PSR4Mapper($this->map2),
        ]);
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
        $mapper  = new MultiMapper([
            new ClassmapMapper($this->map1),
            new PSR4Mapper($this->map2),
        ]);
        $classes = $mapper->mapNamespace('Composer');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function disables_caching_for_all_child_mappers(): void
    {
        $mapper  = new MultiMapper([
            new ClassmapMapper($this->map1),
            new PSR4Mapper($this->map2),
        ]);
        $mappers = $mapper->getMappers();

        foreach ($mappers as $mapper) {
            self::assertFalse($mapper->isCaching());
        }
    }

    /**
     * @test
     */
    public function throws_an_exception_for_paths_in_the_map_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'not-a-path2\' is not a valid path for mapping');

        $mapper = new MultiMapper([
            new ClassmapMapper($this->map1),
            new PSR4Mapper(['not-a-namespace' => ['not-a-path2']]),
        ]);
        $mapper->mapPath('./');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_files_in_the_map_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'not-a-path1\' is not a valid path for mapping');

        $mapper = new MultiMapper([
            new ClassmapMapper(['not-a-namespace' => 'not-a-path1']),
            new PSR4Mapper($this->map2),
        ]);
        $mapper->mapPath('./');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_paths_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'invalid\' is not a valid path for mapping');

        $mapper = new MultiMapper([
            new ClassmapMapper($this->map1),
            new PSR4Mapper($this->map2),
        ]);
        $mapper->mapPath('invalid');
    }
}