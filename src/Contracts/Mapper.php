<?php

namespace Smpl\Inspector\Contracts;

/**
 * Mapper Contract
 *
 * This contract represents a mapper used to map classes for namespaces
 * and paths.
 */
interface Mapper
{
    /**
     * Get a list of classes for the provided path.
     *
     * @param string   $path
     * @param int|null $depth The amount of subdirectory levels to go, if set
     *                        to null, there is no limit.
     *
     * @return list<class-string>
     *
     * @throws \Smpl\Inspector\Exceptions\MapperException
     */
    public function mapPath(string $path, ?int $depth = null): array;

    /**
     * Get a list of classes for the provided namespace.
     *
     * @param string   $namespace
     * @param int|null $depth The amount of sub namespace levels to go, if set
     *                        to null, there is no limit.
     *
     * @return list<class-string>
     *
     * @throws \Smpl\Inspector\Exceptions\MapperException
     */
    public function mapNamespace(string $namespace, ?int $depth = null): array;
}