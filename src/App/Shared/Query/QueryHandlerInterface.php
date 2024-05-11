<?php

namespace Fulll\App\Shared\Query;

interface QueryHandlerInterface
{
    public function __invoke(QueryInterface $command): mixed;
}