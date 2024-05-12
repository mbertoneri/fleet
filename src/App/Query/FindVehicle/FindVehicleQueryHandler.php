<?php

namespace Fulll\App\Query\FindVehicle;

use Fulll\App\Shared\Query\QueryHandlerInterface;
use Fulll\App\Shared\Query\QueryInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Infra\Repository\VehicleRepository;

final readonly class FindVehicleQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ServiceCollectionInterface $serviceCollection
    ) {}

    /**
     * @param FindVehicleQuery $command
     */
    public function __invoke(QueryInterface $command): mixed
    {
        /** @var VehicleRepository $repo */
        $repo = $this->serviceCollection->get(VehicleRepositoryInterface::class);

        return $repo->findByRegistrationPlate($command->plateNumber);

    }
}
