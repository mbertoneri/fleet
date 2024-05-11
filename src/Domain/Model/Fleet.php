<?php

namespace Fulll\Domain\Model;

final class Fleet
{
    /** @var array Vehicle */
    private array $vehicles;

    private function __construct(
        private readonly string $id,
        public readonly string $userId,
    )
    {
        $this->vehicles = [];
    }

    public static function create(string $userId) : static
    {
        return new self(
            id: uniqid('fleet_id',true),
            userId: $userId
        );
    }

    public function registerVehicle(Vehicle $vehicle) : void
    {
        $this->vehicles[$vehicle->getPlateNumber()] = $vehicle;
    }

    public function removeVehicle(Vehicle $vehicle) : void
    {
        if ($this->isVehicleRegistered($vehicle)) {
            unset($this->vehicles[$vehicle->getPlateNumber()]);
        }
    }

    public function isVehicleRegistered(Vehicle $vehicle) : bool
    {
        return isset($this->vehicles[$vehicle->getPlateNumber()]) ;
    }

    public function getAll(): array
    {
        return $this->vehicles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }



}