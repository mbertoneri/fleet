<?php

namespace Fulll\App\Command\ParkVehicle;

use Fulll\App\Shared\Command\CommandInterface;

class ParkVehicleCommand implements CommandInterface
{
    public function __construct(
        public string $vehiclePlateNumber,
        public float  $longitude,
        public float  $latitude,
    ) {}
}
