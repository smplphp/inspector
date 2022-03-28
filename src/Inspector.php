<?php

declare(strict_types=1);

namespace Smpl\Inspector;

class Inspector
{
    private Contracts\TypeFactory $types;

    private Contracts\StructureFactory $structures;

    public function __construct(
        ?Contracts\TypeFactory      $types = null,
        ?Contracts\StructureFactory $structures = null,
    )
    {
        $this->types      = $types ?? Factories\TypeFactory::getInstance();
        $this->structures = $structures ?? Factories\StructureFactory::getInstance();
    }

    public function types(): Contracts\TypeFactory
    {
        return $this->types;
    }

    public function structures(): Contracts\StructureFactory
    {
        return $this->structures;
    }
}