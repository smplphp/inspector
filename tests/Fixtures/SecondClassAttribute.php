<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SecondClassAttribute extends ClassAttribute
{

}