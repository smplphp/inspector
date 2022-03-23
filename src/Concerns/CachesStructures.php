<?php

declare(strict_types=1);

namespace Smpl\Inspector\Concerns;

use Smpl\Inspector\Contracts\Structure;

trait CachesStructures
{
    /**
     * @var array<class-string, \Smpl\Inspector\Contracts\Structure>
     */
    private array $structures = [];

    /**
     * Get an already created structure for the provided class.
     *
     * @param class-string $name
     *
     * @return \Smpl\Inspector\Contracts\Structure|null
     */
    protected function getStructure(string $name): ?Structure
    {
        return $this->structures[$name] ?? null;
    }

    /**
     * Check if a structure has already been created.
     *
     * @param class-string $name
     *
     * @return bool
     */
    protected function hasStructure(string $name): bool
    {
        return $this->getStructure($name) !== null;
    }

    /**
     * Cache a created structure and return it.
     *
     * @param \Smpl\Inspector\Contracts\Structure $structure
     *
     * @return \Smpl\Inspector\Contracts\Structure
     */
    protected function addStructure(Structure $structure): Structure
    {
        $this->structures[$structure->getFullName()] = $structure;
        return $structure;
    }
}