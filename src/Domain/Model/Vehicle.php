<?php

namespace Fulll\Domain\Model;

use Fulll\Domain\Enum\VehicleTypeEnum;

class Vehicle
{
    private function __construct(
        private readonly string          $plateNumber,
        private readonly VehicleTypeEnum $type,
    ){
    }

    public static function createCar(string $registrationNumber): static
    {
        return new self(plateNumber: $registrationNumber, type: VehicleTypeEnum::CAR);
    }

    public static function createMotorcycle(string $registrationNumber): static
    {
        return new self(plateNumber: $registrationNumber, type: VehicleTypeEnum::MOTORCYCLE);
    }

    public static function createTruck(string $registrationNumber): static
    {
        return new self(plateNumber: $registrationNumber, type: VehicleTypeEnum::TRUCK);
    }

    public static function createUnknown(string $registrationNumber): static
    {
        return new self(plateNumber: $registrationNumber, type: VehicleTypeEnum::OTHER);
    }

    public function getPlateNumber(): string
    {
        return $this->plateNumber;
    }

    public function getType(): VehicleTypeEnum
    {
        return $this->type;
    }

}