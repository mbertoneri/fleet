<?php

namespace Fulll\Domain\Model;

use Fulll\Domain\Enum\VehicleTypeEnum;

class Vehicle
{
    private function __construct(
        private readonly string          $id,
        private readonly string          $plateNumber,
        private readonly VehicleTypeEnum $type,
        private ?Location                $location = null,
    ) {}

    public static function create(string $plateNumber, VehicleTypeEnum $type, ?string $id = null): static
    {
        $ensureId = $id ?? uniqid('vehicle-', true);

        return match (true) {
            VehicleTypeEnum::CAR === $type => self::createCar(registrationNumber: $plateNumber, id: $ensureId),
            VehicleTypeEnum::MOTORCYCLE === $type => self::createMotorcycle(registrationNumber: $plateNumber, id: $ensureId),
            VehicleTypeEnum::TRUCK === $type => self::createTruck(registrationNumber: $plateNumber, id: $ensureId),
            VehicleTypeEnum::OTHER === $type => self::createUnknown(registrationNumber: $plateNumber, id: $ensureId),
        };

    }

    public static function createCar(string $registrationNumber, ?string $id = null): static
    {
        return new self(
            id: $id ?? uniqid('veh_', true),
            plateNumber: $registrationNumber,
            type: VehicleTypeEnum::CAR
        );
    }

    public static function createMotorcycle(string $registrationNumber, ?string $id = null): static
    {
        return new self(
            id: $id ?? uniqid('veh_', true),
            plateNumber: $registrationNumber,
            type: VehicleTypeEnum::MOTORCYCLE
        );
    }

    public static function createTruck(string $registrationNumber, ?string $id = null): static
    {
        return new self(
            id: $id ?? uniqid('veh_', true),
            plateNumber: $registrationNumber,
            type: VehicleTypeEnum::TRUCK
        );
    }

    public static function createUnknown(string $registrationNumber, ?string $id = null): static
    {
        return new self(
            id: $id ?? uniqid('veh_', true),
            plateNumber: $registrationNumber,
            type: VehicleTypeEnum::OTHER
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPlateNumber(): string
    {
        return $this->plateNumber;
    }

    public function getType(): VehicleTypeEnum
    {
        return $this->type;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;
        return $this;
    }


}
