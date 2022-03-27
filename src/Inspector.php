<?php

declare(strict_types=1);

namespace Smpl\Inspector;

class Inspector
{
    private static self $instance;

    public static function getInstance(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private Contracts\TypeFactory $types;

    private Contracts\StructureFactory $structures;

    public function __construct(
        ?Contracts\TypeFactory      $types = null,
        ?Contracts\StructureFactory $structures = null,
    )
    {
        $this->types      = $types ?? new Factories\TypeFactory();
        $this->structures = $structures ?? new Factories\StructureFactory($this->types);
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