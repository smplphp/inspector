<?php

declare(strict_types=1);

namespace Smpl\Inspector\Tests\Fixtures;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class TestAttribute
{

}