<?php

namespace App\Enums;

use App\Enums\Enum;

enum RasterDPI: int
{
    use Enum;

    case LOW = 80;
    case HIGH = 360;
}
