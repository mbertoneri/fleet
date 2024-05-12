<?php

namespace Fulll\Domain\Model;

use Fulll\Domain\Enum\VehicleTypeEnum;

class Vehicle
{
    private ?Location                $location = null;

    private function __construct(
        private readonly string          $id,
        private readonly string          $plateNumber,
        private readonly VehicleTypeEnum $type,
    ) {}

    public static function create(string $plateNumber, VehicleTypeEnum $type, ?string $id = null): Vehicle
    {
        $ensureId = $id ?? uniqid('vehicle-', true);

        return new self(
            id: $ensureId,
            plateNumber: $plateNumber,
            type: $type
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
