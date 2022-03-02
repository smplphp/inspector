<?php

declare(strict_types=1);

namespace Smpl\Inspector\Factories;

use RuntimeException;
use Smpl\Inspector\Collections\StructureProperties;
use Smpl\Inspector\Contracts\PropertyFactory as PropertyFactoryContract;
use Smpl\Inspector\Contracts\Structure;
use Smpl\Inspector\Contracts\StructurePropertyCollection;
use Smpl\Inspector\Elements\Property;

class PropertyFactory implements PropertyFactoryContract
{
    private TypeFactory $typeFactory;

    public function __construct(TypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }

    public function make(Structure $structure): StructurePropertyCollection
    {
        if (! $structure->getStructureType()->canHaveProperties()) {
            throw new RuntimeException(sprintf(
                'Structures of type \'%s\' do not have properties',
                $structure->getStructureType()->value
            ));
        }

        $propertyReflections = $structure->getReflection()->getProperties();
        $properties          = [];

        foreach ($propertyReflections as $propertyReflection) {
            $properties[$propertyReflection->getName()] = new Property(
                $structure,
                $propertyReflection,
                $propertyReflection->hasType()
                    ? $this->typeFactory->make($propertyReflection->getType())
                    : null
            );
        }

        return new StructureProperties($structure, $properties);
    }
}