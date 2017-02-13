<?php

namespace Core\Database;

use Core\Utilities\Registry;

class DatabaseConnector {
    private static $instance;
    private $host;
    private $databaseName;
    private $userName;
    private $password;
    private $connection;

    public function __construct () {
        $config = Registry::getInstance()->getDatabaseAccess();
        $this->host = $config["host"];
        $this->databaseName = $config["dbname"];
        $this->userName = $config["user"];
        $this->password = $config["pass"];
        try {
            $this->connect();
        } catch (\Exception $e) {
            $e->getTrace();
        }
    }

    public static function getInstance () {
        if (empty(self::$instance)) {
            self::$instance = new DatabaseConnector();
        }
        return self::$instance;
    }

    private function connect () {
        try {
            $this->connection = new \PDO("mysql:host={$this->host};dbname={$this->databaseName}", $this->userName, $this->password);
            // set the PDO error mode to exception
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function query ($sql, $values = null) {
        $statement = $this->connection->prepare($sql);
        $statement->execute($values);

        $command = substr($sql, 0, strpos($sql, " "));

        switch ($command) {
            case "SELECT":
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            case "INSERT":
                $id = $this->connection->lastInsertId();
                $insertedItems = $statement->rowCount();
                return range($id, $id + $insertedItems - 1);
            case "UPDATE":
            case "DELETE":
                return $statement->rowCount();
            case "BEGIN":
                return $this->connection->beginTransaction();
            case "COMMIT":
                return $this->connection->commit();
        }
    }

    public function getConnection () {
        return $this->connection;
    }
}