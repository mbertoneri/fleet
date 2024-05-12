<?php

namespace Fulll\App\Command\CreateVehicle;

use Fulll\App\Shared\Command\CommandInterface;
use Fulll\Domain\Enum\VehicleTypeEnum;

final readonly class CreateVehicleCommand implements CommandInterface
{
    public function __construct(
        public string          $registrationNumber,
        public VehicleTypeEnum $vehicleType = VehicleTypeEnum::CAR,
    ) {}
}
