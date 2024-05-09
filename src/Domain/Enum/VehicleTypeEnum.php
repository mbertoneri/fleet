<?php

namespace Fulll\Domain\Enum;

enum VehicleTypeEnum: string
{
    case CAR = 'car';
    case MOTORCYCLE = 'motorcycle';
    case TRUCK = 'truck';
    case OTHER = 'other';
}
