<?php

namespace Fulll\Domain\Domain;

use Fulll\Domain\Model\Vehicle;

interface VehicleRepositoryInterface
{
    public function save(Vehicle $vehicle) : void;
    public function findByRegistrationPlate(string $registrationPlate): ?Vehicle;
    public function findByFleetId(string $fleetId): array;
    public function localize(Vehicle $vehicle) : void;
}