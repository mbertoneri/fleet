<?php

declare(strict_types=1);

namespace Fulll\Tests\Integration\RegisterVehicle;

use Behat\Behat\Context\Context;
use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Service\ServiceCollection;

final class RegisterVehicleContext implements Context
{
    private ?Fleet $fleet = null;
    private ?Vehicle $vehicle = null;
    private ?Fleet $anotherFleet = null;
    private bool $registrationTwiceFailed = false;
    private ServiceCollection $services;

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
     * @When I register this vehicle into my fleet
     */
    public function iRegisterThisVehicleIntoMyFleet(): void
    {
        $commandBus = $this->services->getCommandBus();
        $registerCommand = new RegisterVehicleCommand($this->fleet->getId(), $this->vehicle->getPlateNumber());
        $commandBus->execute($registerCommand);
    }

    /**
     * @Then this vehicle should be part of my vehicle fleet
     */
    public function thisVehicleShouldBePartOfMyVehicleFleet(): void
    {
        if (!$this->fleet->isVehicleRegistered($this->vehicle)) {
            throw new \RuntimeException('Vehicle was not registered');
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
    }

    /**
     * @When I try to register this vehicle into my fleet
     */
    public function iTryToRegisterThisVehicleIntoMyFleet(): void
    {
        try {
            $commandBus = $this->services->getCommandBus();
            $registerCommand = new RegisterVehicleCommand($this->fleet->getId(), $this->vehicle->getPlateNumber());
            $commandBus->execute($registerCommand);
        } catch (VehicleAlreadyRegisteredException $exception) {
            $this->registrationTwiceFailed = true;
        }
    }

    /**
     * @Then I should be informed this vehicle has already been registered into my fleet
     */
    public function iShouldBeInformedThisThisVehicleHasAlreadyBeenRegisteredIntoMyFleet(): void
    {
        if (!$this->registrationTwiceFailed) {
            throw new \RuntimeException('VehicleAlreadyRegisteredException expected.');
        }

    }

    /**
     * @Given the fleet of another user
     */
    public function theFleetOfAnotherUser() : void
    {
        $commandBus = $this->services->getCommandBus();
        $createFleetCommand = new CreateFleetCommand('fleetTwo');
        $this->anotherFleet = $commandBus->execute($createFleetCommand);
        if (null === $this->anotherFleet) {
            throw new \RuntimeException('Another Fleet was not created');
        }
    }

    /**
     * @Given this vehicle has been registered into the other user's fleet
     */
    public function thisVehicleHasBeenRegisteredIntoTheOtherUsersFleet() : void
    {
        $commandBus = $this->services->getCommandBus();
        $registerCommand = new RegisterVehicleCommand($this->anotherFleet->getId(), $this->vehicle->getPlateNumber());
        $commandBus->execute($registerCommand);
        if (!$this->anotherFleet->isVehicleRegistered($this->vehicle)){
            throw new \RuntimeException('Vehicle should be registered in another fleet');
        }
    }


}
