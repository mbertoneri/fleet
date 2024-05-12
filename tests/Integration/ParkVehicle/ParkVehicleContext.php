<?php

namespace Fulll\Tests\Integration\ParkVehicle;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommand;
use Fulll\App\Command\ParkVehicle\ParkVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Enum\VehicleTypeEnum;
use Fulll\Domain\Exception\AlreadyParkedException;
use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Location;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Repository\VehicleRepository;
use Fulll\Infra\Service\ServiceCollection;
use Fulll\Tests\Integration\ResetTrait;

final class ParkVehicleContext implements Context
{
    use ResetTrait;

    private ?Fleet $fleet = null;
    private ?Vehicle $vehicle = null;
    private ServiceCollection $services;
    private ?Location $location = null;
    private bool $parkVehicleFailed = false;

    public function __construct()
    {
        $this->services = ServiceCollection::create();
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope): void
    {
        $this->reset($this->services->getSqlManager());
    }

    /**
     * @Given my fleet
     */
    public function myFleet(): void
    {
        $commandBus = $this->services->getCommandBus();
        $createFleetCommand = new CreateFleetCommand('fleetOne');
        /** @var Fleet $fleet */
        $fleet = $commandBus->execute($createFleetCommand);
        $this->fleet = $fleet;
        if (null === $this->fleet) {
            throw new \RuntimeException('Fleet was not created');
        }
    }

    /**
     * @Given a vehicle
     */
    public function aVehicle(): void
    {
        $commandBus = $this->services->getCommandBus();
        $createVehicleCommand = new CreateVehicleCommand(registrationNumber: 'vehicleOne');
        /** @var Vehicle $vehicle */
        $vehicle = $commandBus->execute($createVehicleCommand);
        $this->vehicle = $vehicle;
        if (null === $this->vehicle) {
            throw new \RuntimeException('Fleet was not created');
        }
    }

    /**
     * @Given I have registered this vehicle into my fleet
     */
    public function iHaveRegisteredThisVehicleIntoMyFleet(): void
    {
        if (null === $this->vehicle || null === $this->fleet) {
            throw new \RuntimeException('Fleet or Vehicle should not be null');
        }

        $commandBus = $this->services->getCommandBus();
        $registerCommand = new RegisterVehicleCommand($this->fleet->getUserId(), $this->vehicle->getPlateNumber());
        $commandBus->execute($registerCommand);

        /** @var FleetRepositoryInterface $fleetRepository */
        $fleetRepository = $this->services->get(FleetRepositoryInterface::class);
        $this->fleet = $fleetRepository->findByUserId('fleetOne');

        if (null === $this->fleet) {
            throw new \RuntimeException('Fleet was not found');
        }

        if (!$this->fleet->isVehicleRegistered($this->vehicle)) {
            throw new \RuntimeException('Vehicle was not registered');
        }
    }

    /**
     * @Given a location
     */
    public function aLocation(): void
    {
        $this->location = new Location(latitude: 47.657, longitude: 6.156);
    }

    /**
     * @When I park my vehicle at this location
     */
    public function iParkMyVehicleAtThisLocation(): void
    {
        if (null === $this->vehicle || null === $this->location) {
            throw new \RuntimeException('Vehicle or location was not found');
        }

        $commandBus = $this->services->getCommandBus();
        $parkVehicleCommand = new ParkVehicleCommand(
            vehiclePlateNumber: $this->vehicle->getPlateNumber(),
            longitude: $this->location->getLongitude(),
            latitude: $this->location->getLatitude()
        );
        $commandBus->execute($parkVehicleCommand);
    }

    /**
     * @Then the known location of my vehicle should verify this location
     */
    public function theKnownLocationOfMyVehicleShouldVerifyThisLocation(): void
    {
        /** @var VehicleRepository $vehicleRepository */
        $vehicleRepository = $this->services->get(VehicleRepositoryInterface::class);
        $this->vehicle = $vehicleRepository->findByRegistrationPlate('vehicleOne');

        if (null === $this->vehicle || null === $this->location) {
            throw new \RuntimeException('Vehicle or location was not found');
        }

        if ($this->vehicle->getLocation()?->getLongitude() !== $this->location->getLongitude() ||
            $this->vehicle->getLocation()->getLatitude() !== $this->location->getLatitude()
        ) {
            throw new \RuntimeException('Vehicle is parked at the wrong location');
        }
    }

    /**
     * @Given my vehicle has been parked into this location
     */
    public function myVehicleHasBeenParkedIntoThisLocation(): void
    {
        $this->vehicle = Vehicle::create('At-Ru-cK', VehicleTypeEnum::TRUCK);
        $this->vehicle->setLocation($this->location);

        /** @var VehicleRepositoryInterface $vehicleRepository */
        $vehicleRepository = $this->services->get(VehicleRepositoryInterface::class);
        $vehicleRepository->save($this->vehicle);
    }

    /**
     * @When I try to park my vehicle at this location
     */
    public function iTryToParkMyVehicleAtThisLocation(): void
    {
        if (null === $this->vehicle || null === $this->location) {
            throw new \RuntimeException('Vehicle or location was not found');
        }

        try {
            $commandBus = $this->services->getCommandBus();
            $parkVehicleCommand = new ParkVehicleCommand(
                vehiclePlateNumber: $this->vehicle->getPlateNumber(),
                longitude: $this->location->getLongitude(),
                latitude: $this->location->getLatitude()
            );
            $commandBus->execute($parkVehicleCommand);
        } catch (AlreadyParkedException) {
            $this->parkVehicleFailed = true;
        }
    }

    /**
     * @Then I should be informed that my vehicle is already parked at this location
     */
    public function iShouldBeInformedThatMyVehicleIsAlreadyParkedAtThisLocation(): void
    {
        if (!$this->parkVehicleFailed) {
            throw new \RuntimeException('AlreadyParkedException was not thrown');
        }
    }


}
