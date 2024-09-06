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
                if(!$params["charset"]) $params["charset"] = "utf8mb4";
                $dsn = "mysql:host=". $params["host"]. ";port=". $params["port"]. ";dbname=". $params["database"]. ";charset=". $params["charset"];
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

    private function query2(string $sql, array $params): \PDOStatement{
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
}