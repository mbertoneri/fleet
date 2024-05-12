<?php

namespace Fulll\Infra\Repository;

use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Enum\VehicleTypeEnum;
use Fulll\Domain\Model\Location;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Sql\SqlManagerInterface;

final class VehicleRepository implements VehicleRepositoryInterface
{
    private SqlManagerInterface $manager;

    public function __construct(
        private ServiceCollectionInterface $serviceCollection,
    ) {
        $this->manager = $this->serviceCollection->getSqlManager();
    }

    public function save(Vehicle $vehicle): void
    {
        $query = "INSERT INTO vehicle (id, plate_number, type,latitude,longitude) VALUES (:id, :plateNumber, :type,:latitude, :longitude)";
        $this->manager->insertStmt($query, [
            'id' => $vehicle->getId(),
            'plateNumber' => $vehicle->getPlateNumber(),
            'type' => $vehicle->getType()->value,
            'latitude' => $vehicle->getLocation()?->getLatitude() ?? null,
            'longitude' => $vehicle->getLocation()?->getLongitude() ?? null,
        ]);
    }

    public function findByRegistrationPlate(string $registrationPlate): ?Vehicle
    {
        $query = "SELECT * FROM vehicle WHERE plate_number = :plateNumber";
        $result = $this->manager->fetchStmt($query, ['plateNumber' => $registrationPlate]);

        if (0 === \count($result)) {
            return null;
        }

        $vehicle = Vehicle::create(
            plateNumber: $result[0]['plate_number'],
            type: VehicleTypeEnum::tryFrom($result[0]['type']) ?? throw new \InvalidArgumentException('Vehicle type is invalid'),
            id: $result[0]['id']
        );

        if (null !== ($result[0]['latitude']) && (null !== $result[0]['longitude'])) {
            $vehicle->setLocation(new Location(latitude:$result[0]['latitude'], longitude: $result[0]['longitude']));
        }
        return $vehicle;

    }

    public function findByFleetId(string $fleetId): array
    {
        $vehicles = [];
        $query = "SELECT v.* FROM vehicle v INNER JOIN fleet_vehicle fv on v.id = fv.vehicle_id INNER JOIN main.fleet f on f.id = fv.fleet_id WHERE fleet_id = :fleetId";
        $results = $this->manager->fetchStmt($query, ['fleetId' => $fleetId]);

        foreach ($results as $result) {
            $vehicle = Vehicle::create(
                plateNumber: $result['plate_number'],
                type: VehicleTypeEnum::tryFrom($result['type']) ?? throw new \InvalidArgumentException('Vehicle type is invalid'),
                id: $result['id']
            );

            if (null !== ($result['latitude']) && (null !== $result['longitude'])) {
                $vehicle->setLocation(new Location(latitude:$result['latitude'], longitude: $result['longitude']));
            }

            $vehicles[] = $vehicle;
        }

        return $vehicles;
    }

    public function localize(Vehicle $vehicle): void
    {
        $query = "UPDATE vehicle SET latitude = :latitude, longitude = :longitude WHERE id = :id";
        $this->manager->executeStmt($query, [
            'latitude' => $vehicle->getLocation()?->getLatitude() ?? null,
            'longitude' => $vehicle->getLocation()?->getLongitude() ?? null,
            'id' => $vehicle->getId(),
        ]);
    }
}
