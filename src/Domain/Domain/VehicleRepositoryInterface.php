<?php

namespace Fulll\Domain\Domain;

use Fulll\Domain\Model\Vehicle;

interface VehicleRepositoryInterface
{
    public function save(Vehicle $vehicle);
    public function findByRegistrationPlate(string $registrationPlate): ?Vehicle;
    public function findByFleetId(string $fleetId): array;
}