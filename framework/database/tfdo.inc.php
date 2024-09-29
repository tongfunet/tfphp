<?php

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

class tfdo{
    private tfphp $tfphp;
    private array $params;
    private bool $ready;
    private \PDO $pdo;
    public function __construct(tfphp $tfphp, array $params){
        $this->tfphp = $tfphp;
        $this->params = $params;
        $this->ready = false;
    }
    private function readyTest(){
        if(!$this->ready){
            $this->ready = true;
            switch ($this->params["driver"]){
                default:
                    if(!$this->params["host"]) $this->params["host"] = "localhost";
                    if(!$this->params["port"]) $this->params["port"] = 3306;
                    if(!$this->params["database"]) $this->params["database"] = "";
                    if(!$this->params["username"]) $this->params["username"] = "root";
                    if(!$this->params["password"]) $this->params["password"] = "";
                    if(!$this->params["charset"]) $this->params["charset"] = "utf8mb4";
                    $dsn = "mysql:host=". $this->params["host"]. ";port=". $this->params["port"]. ";dbname=". $this->params["database"]. ";charset=". $this->params["charset"];
                    $this->pdo = new \PDO($dsn, $this->params["username"], $this->params["password"]);
                    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $this->pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
                    $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
                    break;
            }
        }
    }
    private function query(string $sql, array $params): \PDOStatement{
        $this->readyTest();
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $param){
            $stmt->bindParam($param["name"], $param["value"], $param["type"]);
        }
        $stmt->execute();
        return $stmt;
    }
    private function query2(string $sql, array $params): \PDOStatement{
        $this->readyTest();
        $paramsCount = count($params);
        $i = 0;
        $newParams = [];
        while(preg_match("/\?/", $sql, $rg, PREG_OFFSET_CAPTURE)){
            if(($i+1) > $paramsCount){
                throw new \Exception("too few arguments, ". strval($i+1). " are needed and ". strval($paramsCount). " are given");
            }
            $name = ":f". strval($i);
            $sql = substr($sql, 0, $rg[0][1]). $name. substr($sql, $rg[0][1]+strlen($rg[0][0]));
            $newParams[] = [
                "name"=>$name,
                "type"=>\PDO::PARAM_STR,
                "value"=>$params[$i]
            ];
            $i ++;
        }
        if($i < $paramsCount){
            throw new \Exception("too many arguments, ". strval($i). " are needed and ". strval($paramsCount). " are given");
        }
        return $this->query($sql, $newParams);
    }
    private function query3(string $sql, array $params): \PDOStatement{
        $this->readyTest();
        $paramsCount = count($params);
        $i = 0;
        $newParams = [];
        while(preg_match("/\@(int|str)/", $sql, $rg, PREG_OFFSET_CAPTURE)){
            if(($i+1) > $paramsCount){
                throw new \Exception("too few arguments, ". strval($i+1). " are needed and ". strval($paramsCount). " are given");
            }
            $name = ":f". strval($i);
            if($rg[1][0] == "int") $type = \PDO::PARAM_INT;
            else $type = \PDO::PARAM_STR;
            $sql = substr($sql, 0, $rg[0][1]). $name. substr($sql, $rg[0][1]+strlen($rg[0][0]));
            $newParams[] = [
                "name"=>$name,
                "type"=>$type,
                "value"=>$params[$i]
            ];
            $i ++;
        }
        if($i < $paramsCount){
            throw new \Exception("too many arguments, ". strval($i). " are needed and ". strval($paramsCount). " are given");
        }
        return $this->query($sql, $newParams);
    }
    public function beginTransaction(): bool{
        $this->readyTest();
        return $this->pdo->beginTransaction();
    }
    public function commit(): bool{
        $this->readyTest();
        return $this->pdo->commit();
    }
    public function rollback(): bool{
        $this->readyTest();
        return $this->pdo->rollBack();
    }
    public function execute(string $sql, array $params): bool{
        $this->query($sql, $params);
        return true;
    }
    public function execute2(string $sql, array $params): bool{
        $this->query2($sql, $params);
        return true;
    }
    public function execute3(string $sql, array $params): bool{
        $this->query3($sql, $params);
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
    public function fetchOne2(string $sql, array $params): ?array{
        $stmt = $this->query2($sql, $params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row === false){
            return null;
        }
        return $row;
    }
    public function fetchOne3(string $sql, array $params): ?array{
        $stmt = $this->query3($sql, $params);
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
    public function fetchMany2(string $sql, array $params, int $seekBegin, int $fetchNums): ?array{
        switch ($this->params["driver"]){
            default:
                $sql .= " LIMIT ". strval($seekBegin). ",". strval($fetchNums);
        }
        $stmt = $this->query2($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if($rows === false || count($rows) == 0){
            return null;
        }
        return $rows;
    }
    public function fetchMany3(string $sql, array $params, int $seekBegin, int $fetchNums): ?array{
        switch ($this->params["driver"]){
            default:
                $sql .= " LIMIT ". strval($seekBegin). ",". strval($fetchNums);
        }
        $stmt = $this->query3($sql, $params);
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
    public function fetchAll2(string $sql, array $params): ?array{
        $stmt = $this->query2($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if($rows === false || count($rows) == 0){
            return null;
        }
        return $rows;
    }
    public function fetchAll3(string $sql, array $params): ?array{
        $stmt = $this->query3($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if($rows === false || count($rows) == 0){
            return null;
        }
        return $rows;
    }
    public function makePagination(int $total, int $percentpage, int $currentpage): array{
        $totalpage = ceil($total/$percentpage);
        if($currentpage > $totalpage) $currentpage = $totalpage;
        if($currentpage < 1) $currentpage = 1;
        $links = [
            "first"=>(($totalpage > 0 && $currentpage != 1) ? 1 : 0),
            "previous"=>(($totalpage > 0 && $currentpage > 1) ? $currentpage-1 : 0),
            "next"=>(($totalpage > 0 && $currentpage < $totalpage) ? $currentpage+1 : 0),
            "last"=>(($totalpage > 0 && $currentpage != $totalpage) ? $totalpage : 0),
        ];
        return [
            "total"=>$total,
            "percentpage"=>$percentpage,
            "totalpage"=>$totalpage,
            "currentpage"=>$currentpage,
            "links"=>$links,
        ];
    }
    public function getPDO(): \PDO{
        $this->readyTest();
        return $this->pdo;
    }
}