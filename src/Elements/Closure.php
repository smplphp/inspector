<?php

declare(strict_types=1);

namespace Smpl\Inspector\Elements;

use ReflectionFunction;
use Smpl\Inspector\Contracts\Closure as ClosureContract;
use Smpl\Inspector\Contracts\ClosureParameterCollection;
use Smpl\Inspector\Contracts\ParameterFilter;
use Smpl\Inspector\Contracts\Type;
use Smpl\Inspector\Factories\StructureFactory;

class Closure extends BaseFunction implements ClosureContract
{
    private ClosureParameterCollection $parameters;

    public function __construct(ReflectionFunction $reflection, ?Type $type = null)
    {
        parent::__construct($reflection, $type);
    }

    /**
     * @codeCoverageIgnore
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function getReflection(): ReflectionFunction
    {
        return parent::getReflection();
    }

    public function getParameters(?ParameterFilter $filter = null): ClosureParameterCollection
    {
        if (! isset($this->parameters)) {
            $this->parameters = StructureFactory::getInstance()->makeClosureParameters($this);
        }

        if ($filter !== null) {
            return $this->parameters->filter($filter);
        }

        return $this->parameters;
    }
}