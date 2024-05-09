<?php

namespace Fulll\App\Shared\Command;

interface CommandBusInterface
{
    public function execute(CommandInterface $command): mixed;
}