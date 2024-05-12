<?php

namespace Fulll\Infra\Sql;

use Fulll\Infra\Exception\SqlException;
use PDO;

final class SqliteManager implements SqlManagerInterface
{
    public const string DSN = 'sqlite:fleet.db';
    private ?PDO $connection = null;

    public function connect(string $user = '', string $password = '', string $dsn = ''): void
    {
        try {
            $this->connection = new PDO(dsn: $dsn);

        } catch (\PDOException $exception) {
            throw new SqlException($exception->getMessage());
        }
    }

    public function executeStmt(string $sql, array $params = []): void
    {
        $this->checkConnection();

        try {
            // @phpstan-ignore-next-line
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
        } catch (\PDOException $exception) {
            throw new SqlException($exception->getMessage());
        }
    }

    public function insertStmt(string $sql, array $params = []): void
    {
        $this->executeStmt($sql, $params);
    }

    public function fetchStmt(string $sql, array $params = []): array
    {
        $this->checkConnection();

        try {
            // @phpstan-ignore-next-line
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $exception) {
            throw new SqlException($exception->getMessage());
        }
    }

    private function checkConnection(): void
    {
        if (null === $this->connection) {
            throw new SqlException('You must be connected before trying to execute statement');
        }
    }

}
