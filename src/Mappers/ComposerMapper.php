<?php

declare(strict_types=1);

namespace Smpl\Inspector\Mappers;

use Composer\Autoload\ClassLoader;
use Smpl\Inspector\Exceptions\MapperException;

class ComposerMapper extends MultiMapper
{
    private ClassLoader $classLoader;

    /** @noinspection PhpRedundantVariableDocTypeInspection */
    public function __construct()
    {
        $classMap = $this->getClassLoader()->getClassMap();
        /**
         * @var array<class-string, string> $classMap
         */
        $classmapMapper = new ClassmapMapper($classMap);

        if ($this->getClassLoader()->isClassMapAuthoritative()) {
            // This is impossible to test right now without fudging with composer
            // in that way that isn't easily done in code.
            // @codeCoverageIgnoreStart
            parent::__construct([$classmapMapper]);
            // @codeCoverageEnd
        } else {
            $psr4Map = $this->getClassLoader()->getPrefixesPsr4();

            /**
             * @var array<string, list<string>> $psr4Map
             */
            parent::__construct([
                new PSR4Mapper($psr4Map),
                $classmapMapper,
            ]);
        }
    }

    private function loadClassLoader(): void
    {
        $autoloaders = spl_autoload_functions();

        foreach ($autoloaders as $autoloader) {
            if (is_array($autoloader) && isset($autoloader[0]) && $autoloader[0] instanceof ClassLoader) {
                $this->classLoader = $autoloader[0];
                return;
            }
        }

        // This is impossible to test right now without breaking PHPUnit.
        // @codeCoverageIgnoreStart
        throw MapperException::composerClassLoaderMissing();
        // @codeCoverageIgnoreEnd
    }

    private function getClassLoader(): ClassLoader
    {
        if (! isset($this->classLoader)) {
            $this->loadClassLoader();
        }

        return $this->classLoader;
    }
}