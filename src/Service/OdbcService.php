<?php

namespace App\Service;

use PDO;
use PDOException;

class OdbcService
{
    private $dsn;
    private $username;
    private $password;
    private $options;
    private $conn;

    public function __construct(string $dsn, string $username, string $password, array $options = [])
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->conn = new PDO($this->dsn, $this->username, $this->password, $this->options);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception('Connection failed: ' . $e->getMessage());
        }
    }

    public function executeQuery(string $sql): array
    {
        try {
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception('Query failed: ' . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}
