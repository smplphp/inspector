<?php

declare(strict_types=1);

namespace Smpl\Inspector\Support;

enum Visibility
{
    case Public;
    case Protected;
    case Private;
}