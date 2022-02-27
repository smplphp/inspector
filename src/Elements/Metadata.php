<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionAttribute;
use Smpl\Inspector\Contracts\Attribute;
use Smpl\Inspector\Contracts\Metadata as MetadataContract;
use Smpl\Inspector\Contracts\Structure;

class Metadata implements MetadataContract
{
    private ReflectionAttribute $reflection;
    private Attribute           $attribute;
    private object              $instance;
    private ?Structure          $structure;

    public function __construct(ReflectionAttribute $reflection, Attribute $attribute, ?Structure $structure = null)
    {
        $this->attribute  = $attribute;
        $this->reflection = $reflection;
        $this->structure  = $structure;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getArguments(): array
    {
        return $this->reflection->getArguments();
    }

    public function getInstance(): mixed
    {
        if (! isset($this->instance)) {
            $this->instance = $this->reflection->newInstance();
        }

        return $this->instance;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }
}