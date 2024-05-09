<?php

namespace Fulll\Infra\Repository;

use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Exception\VehicleNotFoundException;
use Fulll\Domain\Model\Vehicle;

final class VehicleRepository implements VehicleRepositoryInterface
{
    /** @var array Vehicle */
    private array $vehicles;

    public function __construct()
    {
        $this->vehicles=[];
    }

    public function save(Vehicle $vehicle)
    {
        $this->vehicles[$vehicle->getPlateNumber()] = $vehicle;
    }

    public function findByRegistrationPlate(string $registrationPlate): Vehicle
    {
        foreach ($this->vehicles as $vehicle) {
            if ($vehicle->getPlateNumber() === $registrationPlate) {
                return $vehicle;
            }
        }
        throw new VehicleNotFoundException($registrationPlate);
    }
}