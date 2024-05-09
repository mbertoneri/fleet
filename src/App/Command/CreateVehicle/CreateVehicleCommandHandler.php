<?php

namespace Fulll\App\Command\CreateVehicle;

use Fulll\App\Exception\ServiceNotFoundException;
use Fulll\App\Shared\Command\CommandHandlerInterface;
use Fulll\App\Shared\Command\CommandInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Enum\VehicleTypeEnum;
use Fulll\Domain\Model\Vehicle;

final readonly class CreateVehicleCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ServiceCollectionInterface $serviceCollection
    )
    {
    }

    /**
     * @param CreateVehicleCommand $command
     */
    public function __invoke(CommandInterface $command): mixed
    {
        $vehicleRepository = $this->serviceCollection->get(VehicleRepositoryInterface::class);

        if (null === $vehicleRepository) {
            throw new ServiceNotFoundException('Vehicle repository not found');
        }

        $vehicle = match (true) {
            VehicleTypeEnum::CAR === $command->vehicleType => Vehicle::createCar($command->registrationNumber),
            VehicleTypeEnum::MOTORCYCLE === $command->vehicleType => Vehicle::createMotorcycle($command->registrationNumber),
            VehicleTypeEnum::TRUCK === $command->vehicleType => Vehicle::createTruck($command->registrationNumber),
            VehicleTypeEnum::OTHER === $command->vehicleType => Vehicle::createUnknown($command->registrationNumber),
        };

        $vehicleRepository->save($vehicle);
        return $vehicle;
    }
}