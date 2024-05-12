<?php

namespace Fulll\App\Command\ParkVehicle;

use Fulll\App\Shared\Command\CommandHandlerInterface;
use Fulll\App\Shared\Command\CommandInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Exception\AlreadyParkedException;
use Fulll\Domain\Exception\VehicleNotFoundException;
use Fulll\Domain\Model\Location;
use Fulll\Domain\Model\Vehicle;

class ParkVehicleCommandHandler implements CommandHandlerInterface
{
    public function __construct(private ServiceCollectionInterface $serviceCollection) {}

    /**
     * @param ParkVehicleCommand $command
     */
    public function __invoke(CommandInterface $command): Vehicle
    {
        /** @var VehicleRepositoryInterface $vehicleRepository */
        $vehicleRepository = $this->serviceCollection->get(VehicleRepositoryInterface::class);
        $vehicle = $vehicleRepository->findByRegistrationPlate($command->vehiclePlateNumber);

        if (null === $vehicle) {
            throw new VehicleNotFoundException($command->vehiclePlateNumber);
        }

        //        print_r('****** ParkVehicleCommandHandler');
        //        print_r($vehicle);
        //        print_r($command);

        if ($command->longitude === $vehicle->getLocation()?->getLongitude()
            && $command->latitude === $vehicle->getLocation()?->getLatitude()
        ) {
            throw new AlreadyParkedException($command->vehiclePlateNumber, $command->latitude, $command->longitude);
        }

        $vehicle->setLocation(new Location(latitude: $command->latitude, longitude: $command->longitude));
        $vehicleRepository->localize($vehicle);

        return $vehicle;
    }
}
