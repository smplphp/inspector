<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionAttribute;
use Smpl\Inspector\Contracts\Attribute;
use Smpl\Inspector\Contracts\Metadata as MetadataContract;

/**
 * @template I of object
 * @implements \Smpl\Inspector\Contracts\Metadata<I>
 */
class Metadata implements MetadataContract
{
    private Attribute $attribute;

    /**
     * @var \ReflectionAttribute<I>
     */
    private ReflectionAttribute $reflection;

    /**
     * @param \Smpl\Inspector\Contracts\Attribute $attribute
     * @param \ReflectionAttribute<I>             $reflection
     */
    public function __construct(Attribute $attribute, ReflectionAttribute $reflection)
    {
        $this->attribute  = $attribute;
        $this->reflection = $reflection;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getReflection(): ReflectionAttribute
    {
        return $this->reflection;
    }

    public function getInstance(): object
    {
        return $this->getReflection()->newInstance();
    }
}