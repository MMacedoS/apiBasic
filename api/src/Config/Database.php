<?php

namespace App\Config;

use PDO;

class Database extends Singleton
{
    private ?PDO $pdo;

    protected function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            $config = self::getConfig();
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private static function getConfig(): array
    {
        return [
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
        ];
    }

    public function getConnection(): ?PDO
    {
        return $this->pdo;
    }
}
