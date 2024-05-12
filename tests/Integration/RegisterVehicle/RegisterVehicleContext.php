<?php

declare(strict_types=1);

namespace Fulll\Tests\Integration\RegisterVehicle;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Service\ServiceCollection;
use Fulll\Tests\Integration\ResetTrait;

final class RegisterVehicleContext implements Context
{
    use ResetTrait;

    private ?Fleet $fleet = null;
    private ?Vehicle $vehicle = null;
    private ?Fleet $anotherFleet = null;
    private bool $registrationTwiceFailed = false;
    private ServiceCollection $services;

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
     * @When I register this vehicle into my fleet
     */
    public function iRegisterThisVehicleIntoMyFleet(): void
    {
        if (null === $this->vehicle || null === $this->fleet) {
            throw new \RuntimeException('Fleet or Vehicle should not be null');
        }

        $commandBus = $this->services->getCommandBus();
        $registerCommand = new RegisterVehicleCommand($this->fleet->getUserId(), $this->vehicle->getPlateNumber());
        $commandBus->execute($registerCommand);
    }

    /**
     * @Then this vehicle should be part of my vehicle fleet
     */
    public function thisVehicleShouldBePartOfMyVehicleFleet(): void
    {
        if (null === $this->vehicle) {
            throw new \RuntimeException('Vehicle should not be null');
        }

        /** @var FleetRepositoryInterface $fleetRepository */
        $fleetRepository = $this->services->get(FleetRepositoryInterface::class);
        /** @var Fleet $fleet */
        $fleet = $fleetRepository->findByUserId('fleetOne');
        $this->fleet = $fleet;

        if (!$this->fleet->isVehicleRegistered($this->vehicle)) {
            throw new \RuntimeException('Vehicle was not registered');
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
    }

    /**
     * @When I try to register this vehicle into my fleet
     */
    public function iTryToRegisterThisVehicleIntoMyFleet(): void
    {
        if (null === $this->vehicle || null === $this->fleet) {
            throw new \RuntimeException('Fleet or Vehicle should not be null');
        }

        try {
            $commandBus = $this->services->getCommandBus();
            $registerCommand = new RegisterVehicleCommand($this->fleet->getUserId(), $this->vehicle->getPlateNumber());
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
    public function theFleetOfAnotherUser(): void
    {
        $commandBus = $this->services->getCommandBus();
        $createFleetCommand = new CreateFleetCommand('fleetTwo');
        /** @var Fleet $fleet */
        $fleet = $commandBus->execute($createFleetCommand);
        $this->anotherFleet = $fleet;
        if (null === $this->anotherFleet) {
            throw new \RuntimeException('Another Fleet was not created');
        }
    }

    /**
     * @Given this vehicle has been registered into the other user's fleet
     */
    public function thisVehicleHasBeenRegisteredIntoTheOtherUsersFleet(): void
    {
        if (null === $this->vehicle || null === $this->anotherFleet) {
            throw new \RuntimeException('Fleet or Vehicle should not be null');
        }

        $commandBus = $this->services->getCommandBus();
        $registerCommand = new RegisterVehicleCommand($this->anotherFleet->getUserId(), $this->vehicle->getPlateNumber());
        $commandBus->execute($registerCommand);

        /** @var FleetRepositoryInterface $fleetRepository */
        $fleetRepository = $this->services->get(FleetRepositoryInterface::class);
        $this->anotherFleet = $fleetRepository->findByUserId('fleetTwo');

        if (null === $this->anotherFleet) {
            throw new \RuntimeException('Fleet was not found');
        }

        if (!$this->anotherFleet->isVehicleRegistered($this->vehicle)) {
            throw new \RuntimeException('Vehicle should be registered in another fleet');
        }
    }


}
