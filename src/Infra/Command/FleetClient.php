<?php

namespace Fulll\Infra\Command;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\Domain\Model\Fleet;
use Fulll\Infra\Service\ServiceCollection;
use Fulll\Infra\Sql\SqliteManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FleetClient extends Command
{

    private ServiceCollection $serviceCollection;

    private const string CREATE_FLEET='create';
    private const string REGISTER_VEHICLE='register-vehicle';
    private const string LOCALIZE_VEHICLE='localize-vehicle';

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
            ->addOption('userId', null, InputOption::VALUE_REQUIRED, 'fleet user id');

//            ->addArgument('register-vehicle', InputArgument::OPTIONAL, 'Register a new fleet')//TODO
//            ->addArgument('localize-vehicle', InputArgument::OPTIONAL, 'Localize a vehicle');//TODO
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Fleet client');

        $argument = $input->getArgument('create');

        if (empty($argument)){
            $io->error('No arguments was provided');
        }

        $io->writeln('Argument '. $argument);

        switch ($argument) {
            case self::CREATE_FLEET:
                $this->createFleet($input,$io);
                break;
            default:
                break;
        }

        return Command::SUCCESS;
    }


    private function createFleet(InputInterface $input, SymfonyStyle $io): void
    {
        $userId = $input->getOption('userId');
        $io->writeln('userId is '. $userId);
        if (empty($userId)){
            $io->error('Can\t create a fleet without userId');
        }

        try {
            $io->writeln('creating fleet');
            /** @var Fleet $fleet */
            $fleet = $this->serviceCollection->getCommandBus()->execute(new CreateFleetCommand($userId));

            $io->success('Fleet with user id ' . $fleet->getUserId() . ' was successfully created');
        }catch (\Exception $exception){
            $io->error('An error occured while creating fleet: '.$exception->getMessage());
        }
    }

}