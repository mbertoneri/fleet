<?php

namespace Fulll\Tests\Integration\ParkVehicle;

use Behat\Behat\Context\Context;
use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommand;
use Fulll\App\Command\ParkVehicle\ParkVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Exception\AlreadyParkedException;
use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Location;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Service\ServiceCollection;

final class ParkVehicleContext implements Context
{
    private ?Fleet $fleet = null;
    private ?Vehicle $vehicle = null;
    private ServiceCollection $services;
    private ?Location $location = null;
    private bool $parkVehicleFailed = false;

    public function __construct()
    {
        $this->services = ServiceCollection::create();
    }

    /**
     * @Given my fleet
     */
    public function myFleet(): void
    {
        $commandBus = $this->services->getCommandBus();
        $createFleetCommand = new CreateFleetCommand('fleetOne');
        $this->fleet = $commandBus->execute($createFleetCommand);
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
        $this->vehicle = $commandBus->execute($createVehicleCommand);
        if (null === $this->vehicle) {
            throw new \RuntimeException('Fleet was not created');
        }
    }

    /**
     * @Given I have registered this vehicle into my fleet
     */
    public function iHaveRegisteredThisVehicleIntoMyFleet(): void
    {
        $commandBus = $this->services->getCommandBus();
        $registerCommand = new RegisterVehicleCommand($this->fleet->getId(), $this->vehicle->getPlateNumber());
        $commandBus->execute($registerCommand);

        if (!$this->fleet->isVehicleRegistered($this->vehicle->getPlateNumber())) {
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
        $commandBus = $this->services->getCommandBus();
        $parkVehicleCommand = new ParkVehicleCommand(
            vehiclePlateNumber: $this->vehicle?->getPlateNumber(),
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
        if ($this->vehicle->getLocation()?->getLongitude() !== $this->location->getLongitude() ||
            $this->vehicle->getLocation()?->getLatitude() !== $this->location->getLatitude()
        ) {
            throw new \RuntimeException('Vehicle is parked at the wrong location');
        }
    }

    /**
     * @Given my vehicle has been parked into this location
     */
    public function myVehicleHasBeenParkedIntoThisLocation(): void
    {
        $this->vehicle = Vehicle::createTruck('AtRucK');
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
        try {
            $commandBus = $this->services->getCommandBus();
            $parkVehicleCommand = new ParkVehicleCommand(
                vehiclePlateNumber: $this->vehicle?->getPlateNumber(),
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