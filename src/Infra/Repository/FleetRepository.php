<?php

namespace Fulll\Infra\Repository;

use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Sql\SqlManagerInterface;

final class FleetRepository implements FleetRepositoryInterface
{
    private SqlManagerInterface $manager;

    public function __construct(
        private ServiceCollectionInterface $serviceCollection,
    ) {
        $this->manager = $this->serviceCollection->getSqlManager();
    }

    public function save(Fleet $fleet): void
    {
        if (null === $this->findByUserId($fleet->getUserId())) {
            $query = "INSERT INTO fleet (id, user_id) VALUES (:id, :userId)";
            $this->manager->insertStmt($query, ['id' => $fleet->getId(), 'userId' => $fleet->getUserId()]);
        }

        /** @var Vehicle $vehicle */
        foreach ($fleet->getAll() as $vehicle) {
            $query = "INSERT OR IGNORE INTO fleet_vehicle (fleet_id, vehicle_id) VALUES (:fleetId, :vehicleId)";
            $this->manager->insertStmt(
                $query,
                [
                    'fleetId' => $fleet->getId(),
                    'vehicleId' => $vehicle->getId(),
                ]
            );
        }
    }

    public function findByUserId(string $userId): ?Fleet
    {
        $query = "SELECT * FROM fleet WHERE user_id = :userId";
        $result = $this->manager->fetchStmt($query, ['userId' => $userId]);

        if (0 === \count($result)) {
            return null;
        }

        $fleet = Fleet::create(userId: $result[0]['user_id'], id: $result[0]['id']);


        /** @var VehicleRepositoryInterface $vehicleRepository */
        $vehicleRepository = $this->serviceCollection->get(VehicleRepositoryInterface::class);
        $vehicles = $vehicleRepository->findByFleetId($fleet->getId());

        foreach ($vehicles as $vehicle) {
            $fleet->registerVehicle($vehicle);
        }

        return $fleet;
    }

}
