<?php

namespace Fulll\App\Command\RegisterVehicle;

use Fulll\App\Shared\Command\CommandInterface;

final readonly class RegisterVehicleCommand implements CommandInterface
{
    public function __construct(
        public string $fleetUserId,
        public string $vehiclePlateNumber,
    ) {}
}
