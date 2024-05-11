<?php

namespace Fulll\App\Command\RegisterVehicle;

use Fulll\App\Shared\Command\CommandHandlerInterface;
use Fulll\App\Shared\Command\CommandInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Exception\VehicleNotFoundException;
use Fulll\Domain\Model\Vehicle;

final readonly class RegisterVehicleCommandHandler implements CommandHandlerInterface
{

    public function __construct(private ServiceCollectionInterface $serviceCollection)
    {
    }

    /**
     * @param RegisterVehicleCommand $command
     */
    public function __invoke(CommandInterface $command): Vehicle
    {
        /** @var FleetRepositoryInterface $fleetRepository */
        $fleetRepository = $this->serviceCollection->get(FleetRepositoryInterface::class);
        $fleet = $fleetRepository->findByUserId($command->fleetUserId);

        if (null === $fleet){
            throw new FleetNotFoundException($command->fleetUserId);
        }

        /** @var VehicleRepositoryInterface $vehicleRepository */
        $vehicleRepository = $this->serviceCollection->get(VehicleRepositoryInterface::class);
        $vehicle = $vehicleRepository->findByRegistrationPlate($command->vehiclePlateNumber);

        if (null === $vehicle) {
            throw new VehicleNotFoundException($command->vehiclePlateNumber);
        }

        if ($fleet->isVehicleRegistered($vehicle)){
            throw new VehicleAlreadyRegisteredException($vehicle);
        }

        $fleet->registerVehicle($vehicle);

        $fleetRepository->save($fleet);
        return $vehicle;
    }

}