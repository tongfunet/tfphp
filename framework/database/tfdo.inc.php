<?php

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

class tfdo{
    private tfphp $tfphp;
    private array $params;
    private \PDO $pdo;
    public function __construct(tfphp $tfphp, array $params){
        $this->tfphp = $tfphp;
        $this->params = $params;
        switch ($params["driver"]){
            default:
                if(!$params["host"]) $params["host"] = "localhost";
                if(!$params["port"]) $params["port"] = 3306;
                if(!$params["database"]) $params["database"] = "";
                if(!$params["username"]) $params["username"] = "root";
                if(!$params["password"]) $params["password"] = "";
                $dsn = "mysql:host=". $params["host"]. ";port=". $params["port"]. ";dbname=". $params["database"];
                $this->pdo = new \PDO($dsn, $params["username"], $params["password"]);
                $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
                $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
                break;
        }
    }

    public function beginTransaction(): bool{
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool{
        return $this->pdo->commit();
    }

    public function rollback(): bool{
        return $this->pdo->rollBack();
    }

    private function query(string $sql, array $params): \PDOStatement{
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $param){
            $stmt->bindParam($param["name"], $param["value"], $param["type"]);
        }
        $stmt->execute();
        return $stmt;
    }

    public function execute(string $sql, array $params): bool{
        $this->query($sql, $params);
        return true;
    }

    public function fetchOne(string $sql, array $params): ?array{
        $stmt = $this->query($sql, $params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row === false){
            return null;
        }
        return $row;
    }

    public function fetchMany(string $sql, array $params, int $seekBegin, int $fetchNums): ?array{
        switch ($this->params["driver"]){
            default:
                $sql .= " LIMIT ". strval($seekBegin). ",". strval($fetchNums);
        }
        $stmt = $this->query($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if($rows === false || count($rows) == 0){
            return null;
        }
        return $rows;
    }

    public function fetchAll(string $sql, array $params): ?array{
        $stmt = $this->query($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if($rows === false || count($rows) == 0){
            return null;
        }
        return $rows;
    }
}