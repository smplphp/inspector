<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionAttribute;
use Smpl\Inspector\Contracts\Attribute;
use Smpl\Inspector\Contracts\Metadata as MetadataContract;

/**
 * @template T of Structure|Method|Property|Parameter
 * @template I of object
 * @implements \Smpl\Inspector\Contracts\Metadata<I>
 * @psalm-suppress ImplementedReturnTypeMismatch
 * @psalm-suppress InvalidReturnType
 * @psalm-suppress InvalidReturnStatement
 */
class Metadata implements MetadataContract
{
    private Attribute           $attribute;
    private ReflectionAttribute $reflection;

    /**
     * @param \Smpl\Inspector\Contracts\Attribute $attribute
     * @param \ReflectionAttribute                $reflection
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

    /**
     * @return I
     */
    public function getInstance(): object
    {
        return $this->getReflection()->newInstance();
    }
}