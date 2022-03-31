<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Mappers;

use PHPUnit\Framework\TestCase;
use Smpl\Inspector\Contracts\StructureFactory;
use Smpl\Inspector\Elements\Structure;
use Smpl\Inspector\Exceptions\MapperException;
use Smpl\Inspector\Inspection;
use Smpl\Inspector\Inspector;
use Smpl\Inspector\Mappers\ClassmapMapper;

/**
 * @group mappers
 * @group classmap
 */
class ClassmapMapperTest extends TestCase
{
    /**
     * @var array<class-string, string>
     */
    private array $classmap;

    protected function setUp(): void
    {
        $this->classmap = [
            Inspector::class        => './src/Inspector.php',
            Inspection::class       => './src/Inspection.php',
            StructureFactory::class => './src/Contracts/StructureFactory.php',
            Structure::class        => './src/Elements/Structure.php',
        ];
    }

    /**
     * @test
     */
    public function only_loads_classes_from_classmap_by_path(): void
    {
        $mapper   = new ClassmapMapper($this->classmap);
        $classes1 = $mapper->mapPath('./');
        $classes2 = $mapper->mapPath('./src/Elements');

        self::assertCount(4, $classes1);
        self::assertSame(Inspector::class, $classes1[0]);
        self::assertSame(Inspection::class, $classes1[1]);
        self::assertSame(StructureFactory::class, $classes1[2]);
        self::assertSame(Structure::class, $classes1[3]);

        self::assertCount(1, $classes2);
        self::assertSame(Structure::class, $classes2[0]);
    }

    /**
     * @test
     */
    public function returns_an_empty_array_for_paths_outside_of_its_classmap(): void
    {
        $mapper  = new ClassmapMapper($this->classmap);
        $classes = $mapper->mapPath('./src/Factories');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function only_loads_classes_from_classmap_by_namespace(): void
    {
        $mapper   = new ClassmapMapper($this->classmap);
        $classes1 = $mapper->mapNamespace('Smpl');
        $classes2 = $mapper->mapNamespace('Smpl\Inspector\Elements');

        self::assertCount(4, $classes1);
        self::assertSame(Inspector::class, $classes1[0]);
        self::assertSame(Inspection::class, $classes1[1]);
        self::assertSame(StructureFactory::class, $classes1[2]);
        self::assertSame(Structure::class, $classes1[3]);

        self::assertCount(1, $classes2);
        self::assertSame(Structure::class, $classes2[0]);
    }

    /**
     * @test
     */
    public function returns_an_empty_array_for_namespaces_outside_of_its_classmap(): void
    {
        $mapper  = new ClassmapMapper($this->classmap);
        $classes = $mapper->mapNamespace('Smpl\Inspector\Factories');

        self::assertCount(0, $classes);
    }

    /**
     * @test
     */
    public function throws_an_exception_for_paths_in_the_classmap_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'not-a-path\' is not a valid path for mapping');

        $mapper = new ClassmapMapper(['not-a-class' => 'not-a-path']);
        $mapper->mapPath('./');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_files_in_the_classmap_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'./src/test.php\' is not a valid path for mapping');

        $mapper = new ClassmapMapper(['not-a-class' => './src/test.php']);
        $mapper->mapPath('./');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_classes_mapped_to_a_directory(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'./src\' is not a valid file');

        $mapper = new ClassmapMapper([Inspector::class => './src']);
        $mapper->mapPath('./');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_classes_in_the_classmap_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided class \'invalid\' does not exist, and cannot be autoloaded');

        $mapper = new ClassmapMapper(['invalid' => './src/Inspection.php']);
        $mapper->mapPath('./src');
    }

    /**
     * @test
     */
    public function throws_an_exception_for_paths_that_dont_exist(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('The provided path \'./invalid\' is not a valid path for mapping');

        $mapper = new ClassmapMapper($this->classmap);
        $mapper->mapPath('./invalid');
    }
}