<?php

namespace Fulll\Infra\Command;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommand;
use Fulll\App\Command\ParkVehicle\ParkVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Query\FindFleet\FindFleetQuery;
use Fulll\App\Query\FindVehicle\FindVehicleQuery;
use Fulll\Domain\Enum\VehicleTypeEnum;
use Fulll\Domain\Exception\VehicleNotFoundException;
use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Vehicle;
use Fulll\Infra\Service\ServiceCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FleetClient extends Command
{
    private ServiceCollection $serviceCollection;

    private const string CREATE_FLEET = 'create';
    private const string REGISTER_VEHICLE = 'register-vehicle';
    private const string LOCALIZE_VEHICLE = 'localize-vehicle';

    public function __construct()
    {
        parent::__construct('fleet:client');

        $this->serviceCollection = ServiceCollection::create();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Fleet client.');

        $this
            // configure an argument
            ->addArgument('create', InputArgument::OPTIONAL, 'Create a new fleet')
            ->addOption('userId', null, InputOption::VALUE_OPTIONAL, 'fleet user id')
            ->addArgument('register-vehicle', InputArgument::OPTIONAL, 'Register a new vehicle')
            ->addOption('plate-number', null, InputOption::VALUE_OPTIONAL, 'Vehicle plate number')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'Vehicle type', VehicleTypeEnum::CAR->value)
            ->addArgument('localize-vehicle', InputArgument::OPTIONAL, 'Localize a vehicle')
            ->addOption('lat', null, InputOption::VALUE_OPTIONAL, 'Latitude')
            ->addOption('lng', null, InputOption::VALUE_OPTIONAL, 'Longitude');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Fleet client');

        /** @var string $argument */
        $argument = $input->getArgument('create');

        if ($this->isEmpty($argument)) {
            $io->error('No arguments was provided');
        }

        $io->writeln('Argument ' . $argument);

        switch ($argument) {
            case self::CREATE_FLEET:
                $this->createFleet($input, $io);
                break;
            case self::REGISTER_VEHICLE:
                $this->createVehicle($input, $io);
                break;
            case self::LOCALIZE_VEHICLE:
                $this->localizeVehicle($input, $io);
                break;
            default:
                $io->error('No arguments provided');
                break;
        }

        return Command::SUCCESS;
    }


    private function createFleet(InputInterface $input, SymfonyStyle $io): void
    {
        //php bin/console fleet:client create --userId fleet-one

        if ($this->isEmpty($input->getOption('userId'))) {
            $io->error('Can\t create a fleet without userId');
            return;
        }

        /** @var string $userId */
        $userId = $input->getOption('userId');

        try {
            $io->writeln('creating fleet');
            /** @var Fleet $fleet */
            $fleet = $this->serviceCollection->getCommandBus()->execute(new CreateFleetCommand($userId));

            $io->success('Fleet with user id ' . $fleet->getUserId() . ' was successfully created');
        } catch (\Exception $exception) {
            $io->error('An error occured while creating fleet: ' . $exception->getMessage());
        }
    }

    private function createVehicle(InputInterface $input, SymfonyStyle $io): void
    {
        //php bin/console fleet:client register-vehicle --userId fleet-one --plate-number AX-3K-OK --type motorcycle

        if ($this->isEmpty($input->getOption('userId')) || $this->isEmpty($input->getOption('plate-number')) || $this->isEmpty($input->getOption('type'))) {
            $io->error('Can\t create a vehicle without fleet user id or vehicle plate number or type');
            return;
        }

        /** @var string $userId */
        $userId = $input->getOption('userId');
        /** @var string $plateNumber */
        $plateNumber = $input->getOption('plate-number');
        /** @var string $type */
        $type = $input->getOption('type');

        try {

            $queryBus = $this->serviceCollection->getQueryBus();
            $commandBus = $this->serviceCollection->getCommandBus();

            /** @var Fleet $fleet */
            $fleet = $queryBus->ask(new FindFleetQuery($userId));
            $vehicle = $queryBus->ask(new FindVehicleQuery($plateNumber));

            if (null === $vehicle) {
                $vehicle = $commandBus->execute(new CreateVehicleCommand($plateNumber, VehicleTypeEnum::tryFrom($type) ?? VehicleTypeEnum::CAR));
            }

            /** @var Vehicle $vehicle */
            $commandBus->execute(new RegisterVehicleCommand($fleet->getUserId(), $vehicle->getPlateNumber()));

            $io->success('Vehicle with plate number ' . $plateNumber . ' was successfully registered');

        } catch (\Exception $exception) {
            $io->error('An error occurred while creating vehicle: ' . $exception->getMessage());
        }

    }

    private function localizeVehicle(InputInterface $input, SymfonyStyle $io): void
    {
        //php bin/console fleet:client localize-vehicle --plate-number AX-3K-OK --lat 3.45 --lng 18.456

        if ($this->isEmpty($input->getOption('plate-number')) || $this->isEmpty($input->getOption('lat')) || $this->isEmpty($input->getOption('lng'))) {
            $io->error("Can't localize vehicle without plate number or lat and lng");
            return;
        }

        /** @var string $plateNumber */
        $plateNumber = $input->getOption('plate-number');
        /** @var string $latitude */
        $latitude = $input->getOption('lat');
        /** @var string $longitude */
        $longitude = $input->getOption('lng');

        try {
            $queryBus = $this->serviceCollection->getQueryBus();
            $vehicle = $queryBus->ask(new FindVehicleQuery($plateNumber));

            if (null === $vehicle) {
                throw new VehicleNotFoundException($plateNumber);
            }

            $commandBus = $this->serviceCollection->getCommandBus();
            /** @var Vehicle $vehicle */
            $commandBus->execute(new ParkVehicleCommand(vehiclePlateNumber: $vehicle->getPlateNumber(), longitude:  (float) $longitude, latitude:  (float) $latitude));

            $io->success('Vehicle with plate number ' . $plateNumber . ' was successfully parked at [lat=' . $latitude . ',lng=' . $longitude . '] ');


        } catch (\Exception $exception) {
            $io->error('An error occurred while localizing vehicle: ' . $exception->getMessage());
        }

    }

    private function isEmpty(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }
        if (is_string($value) && $value === '') {
            return true;
        }
        return false;
    }

}
