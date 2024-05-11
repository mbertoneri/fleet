<?php

namespace Fulll\App\Query\FindFleet;

use Fulll\App\Shared\Query\QueryHandlerInterface;
use Fulll\App\Shared\Query\QueryInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Model\Fleet;
use Fulll\Infra\Repository\FleetRepository;

final readonly class FindFleetQueryHandler implements QueryHandlerInterface
{

    public function __construct(
        private ServiceCollectionInterface $serviceCollection
    ){}

    /**
     * @param FindFleetQuery $command
     */
    public function __invoke(QueryInterface $command): Fleet
    {
        /** @var FleetRepository $repo */
        $repo = $this->serviceCollection->get(FleetRepositoryInterface::class);

        return $repo->findByUserId($command->userId);

    }
}