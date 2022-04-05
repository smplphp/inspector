<?php
/**
 * Inspector helpers
 */
namespace Smpl\Inspector {

    /**
     * Get the current instance of the inspector.
     *
     * If no instance is presence, the values provided as arguments will be
     * used to create one.
     *
     * @param \Smpl\Inspector\Contracts\TypeFactory|null      $types
     * @param \Smpl\Inspector\Contracts\StructureFactory|null $structures
     * @param \Smpl\Inspector\Contracts\Mapper|null           $mapper
     *
     * @return \Smpl\Inspector\Inspector
     */
    function inspector(
        ?Contracts\TypeFactory      $types = null,
        ?Contracts\StructureFactory $structures = null,
        Contracts\Mapper            $mapper = null
    ): Inspector
    {
        return Inspector::getInstance($types, $structures, $mapper);
    }

    /**
     * Start a new inspection.
     *
     * @return \Smpl\Inspector\Inspection
     */
    function inspection(): Inspection
    {
        return inspector()->inspect();
    }
}