<?php

namespace Fulll\Infra\Sql;

interface SqlManagerInterface
{
    public function connect(string $user='', string $password='', string $dsn=''): void;

    public function insertStmt(string $sql, array $params = []): void;

    public function executeStmt(string $sql, array $params = []): void;

    public function fetchStmt(string $sql, array $params = []): array;
}