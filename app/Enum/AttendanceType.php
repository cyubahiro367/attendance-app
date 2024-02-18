<?php

declare(strict_types=1);

namespace App\Enum;

enum AttendanceType: int
{
    case ARRIVE = 1;
    case LEAVE = 2;
}