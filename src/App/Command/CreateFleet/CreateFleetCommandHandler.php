<?php

namespace Fulll\App\Command\CreateFleet;

use Fulll\App\Shared\Command\CommandHandlerInterface;
use Fulll\App\Shared\Command\CommandInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Model\Fleet;

final readonly class CreateFleetCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ServiceCollectionInterface $serviceCollection
    ) {}

    /**
     * @param CreateFleetCommand $command
     */
    public function __invoke(CommandInterface $command): Fleet
    {
        /** @var FleetRepositoryInterface $fleetRepository */
        $fleetRepository = $this->serviceCollection->get(FleetRepositoryInterface::class);

        $fleet = Fleet::create($command->userId);
        $fleetRepository->save($fleet);

        return $fleet;
    }

}
