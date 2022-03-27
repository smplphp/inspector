<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ClassAttribute implements AttributeInterface
{

}