<?php

namespace Fulll\Infra\Command;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Query\FindFleet\FindFleetQuery;
use Fulll\App\Query\FindVehicle\FindVehicleQuery;
use Fulll\Domain\Model\Fleet;
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
            ->addOption('plate-number', null, InputOption::VALUE_OPTIONAL, 'Vehicle plate number');
//            ->addArgument('localize-vehicle', InputArgument::OPTIONAL, 'Localize a vehicle');//TODO
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Fleet client');

        $argument = $input->getArgument('create');

        if (empty($argument)) {
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
            default:
                break;
        }

        return Command::SUCCESS;
    }


    private function createFleet(InputInterface $input, SymfonyStyle $io): void
    {
        //php bin/console fleet:client create --userId fleet-one
        $userId = $input->getOption('userId');
        if (empty($userId)) {
            $io->error('Can\t create a fleet without userId');
        }

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
        //fleet:client register-vehicle --userId fleet-one --plate-number AX-3K-OK

        $userId = $input->getOption('userId');
        $plateNumber = $input->getOption('plate-number');

        if (empty($plateNumber) || empty($userId)) {
            $io->error('Can\t create a vehicle without fleet user id or vehicle plate number');
        }

        try {

            $queryBus = $this->serviceCollection->getQueryBus();
            $commandBus = $this->serviceCollection->getCommandBus();

            /** @var Fleet $fleet */
            $fleet = $queryBus->ask(new FindFleetQuery($userId));
            $vehicle = $queryBus->ask(new FindVehicleQuery($plateNumber));

            if (null ===$vehicle){
                $vehicle = $commandBus->execute(new CreateVehicleCommand($plateNumber));
            }

            $commandBus->execute(new RegisterVehicleCommand($fleet->getUserId(), $vehicle->getPlateNumber()));

        } catch (\Exception $exception){
            $io->error('An error occured while creating vehicle: ' . $exception->getMessage());
        }

    }

}