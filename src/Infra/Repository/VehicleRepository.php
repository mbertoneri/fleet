<?php

namespace Fulll\Infra\Repository;

use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Enum\VehicleTypeEnum;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Sql\SqlManagerInterface;

final class VehicleRepository implements VehicleRepositoryInterface
{
    private SqlManagerInterface $manager;

    public function __construct(
        private ServiceCollectionInterface $serviceCollection,
    )
    {
        $this->manager = $this->serviceCollection->getSqlManager();
    }

    public function save(Vehicle $vehicle)
    {
        $id = uniqid('vehicle-', true);
        $query = "INSERT INTO vehicle (id, plate_number, type) VALUES (:id, :plateNumber, :type)";
        $this->manager->insertStmt($query, [
            'id' => $id,
            'plateNumber' => $vehicle->getPlateNumber(),
            'type' => $vehicle->getType()->value]);
    }

    public function findByRegistrationPlate(string $registrationPlate): ?Vehicle
    {
        $query = "SELECT * FROM vehicle WHERE plate_number = :plateNumber";
        $result = $this->manager->fetchStmt($query, ['plateNumber' => $registrationPlate]);

        if (0 === \count($result)) {
            return null;
        }

        return Vehicle::create(plateNumber: $result[0]['plate_number'], type: VehicleTypeEnum::tryFrom($result[0]['type']), id: $result[0]['id']);

    }

    public function findByFleetId(string $fleetId): array
    {
        $vehicles=[];
        $query = "SELECT v.* FROM vehicle v INNER JOIN fleet_vehicle fv on v.id = fv.vehicle_id INNER JOIN main.fleet f on f.id = fv.fleet_id WHERE fleet_id = :fleetId";
        $results = $this->manager->fetchStmt($query, ['fleetId' => $fleetId]);

        foreach ($results as $result){
            $vehicles[]=Vehicle::create(plateNumber: $result['plate_number'], type: VehicleTypeEnum::tryFrom($result['type']), id: $result['id']);
        }

        return $vehicles;
    }
}