<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

/**
 * Class tfdo
 * @package tfphp\framework\database
 * @datetime 2025/7/17
 */
class tfdo{
    private tfphp $tfphp;
    private array $params;
    private bool $ready;
    private int $transactionLevel;
    private \PDO $pdo;
    public function __construct(tfphp $tfphp, array $params){
        $this->tfphp = $tfphp;
        $this->params = $params;
        $this->ready = false;
        $this->transactionLevel = 0;
    }
    private function readyTest(){
        if(!$this->ready){
            $this->ready = true;
            if(empty($this->params["driver"])) $this->params["driver"] = "mysql";
            switch ($this->params["driver"]){
                case "mysql":
                    if(empty($this->params["host"])) $this->params["host"] = "localhost";
                    if(empty($this->params["port"])) $this->params["port"] = 3306;
                    if(empty($this->params["database"])) $this->params["database"] = "";
                    if(empty($this->params["username"])) $this->params["username"] = "root";
                    if(empty($this->params["password"])) $this->params["password"] = "";
                    if(empty($this->params["charset"])) $this->params["charset"] = "utf8mb4";
                    $dsn = "mysql:host=". $this->params["host"]. ";port=". $this->params["port"]. ";dbname=". $this->params["database"]. ";charset=". $this->params["charset"];
                    $this->pdo = new \PDO($dsn, $this->params["username"], $this->params["password"]);
                    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $this->pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
                    $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
                    break;
                default:
                    throw new \Exception("invalid type ". $this->params["driver"]. " for TFDO", 660101);
            }
        }
    }
    private function query(string $sql, array $params): \PDOStatement{
        $this->readyTest();
        // from xxx
        // join xxx
        // insert into xxx
        // update xxx
        // create/drop/alter/truncate table xxx
        $sql = preg_replace_callback("/((?:from|join|into|update|table|desc)[\s\t\r\n]+)(\^)?([a-zA-Z0-9\_]+)(\\$)?/", function(array $mats){
            $needPrefix = ($mats[2]);
            $needSuffix = (count($mats) == 5);
            return $mats[1]. (($needPrefix)?strval($this->params["table_prefix"]):""). $mats[3]. (($needSuffix)?strval($this->params["table_suffix"]):"");
        }, $sql);
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
        $newParams = [];
        $i = 0;
        while(preg_match("/\?/", $sql, $rg, PREG_OFFSET_CAPTURE)){
            if(($i+1) > $paramsCount){
                throw new \Exception("too few arguments, ". strval($i+1). " are needed and ". strval($paramsCount). " are given", 660102);
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
            throw new \Exception("too many arguments, ". strval($i). " are needed and ". strval($paramsCount). " are given", 660103);
        }
        return $this->query($sql, $newParams);
    }
    private function query3(string $sql, array $params): \PDOStatement{
        $this->readyTest();
        $paramsCount = count($params);
        $newParams = [];
        $i = 0;
        while(preg_match("/\@(int|str)/", $sql, $rg, PREG_OFFSET_CAPTURE)){
            if(($i+1) > $paramsCount){
                throw new \Exception("too few arguments, ". strval($i+1). " are needed and ". strval($paramsCount). " are given", 660104);
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
            throw new \Exception("too many arguments, ". strval($i). " are needed and ". strval($paramsCount). " are given", 660105);
        }
        return $this->query($sql, $newParams);
    }
    private function doExecute(string $sql, array $params): bool{
        $this->query($sql, $params);
        return true;
    }
    private function doFetchOne(\PDOStatement $stmt): ?array{
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row === false){
            return null;
        }
        return $row;
    }
    private function doFetchMany(\PDOStatement $stmt): array{
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if($rows === false || count($rows) == 0){
            return [];
        }
        return $rows;
    }
    private function doFetchAll(\PDOStatement $stmt): array{
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if($rows === false || count($rows) == 0){
            return [];
        }
        return $rows;
    }
    public function beginTransaction(): bool{
        $this->readyTest();
        if($this->transactionLevel == 0){
            $ret = $this->pdo->beginTransaction();
        }
        else{
            $ret = $this->pdo->exec("SAVEPOINT LEVEL". $this->transactionLevel);
        }
        $this->transactionLevel ++;
        return $ret;
    }
    public function commit(): bool{
        $this->readyTest();
        if($this->transactionLevel > 0){
            $this->transactionLevel --;
            if($this->transactionLevel == 0){
                return $this->pdo->commit();
            }
        }
        return false;
    }
    public function rollback(): bool{
        $this->readyTest();
        if($this->transactionLevel > 0){
            $this->transactionLevel --;
            if($this->transactionLevel == 0){
                return $this->pdo->rollBack();
            }
            else{
                return $this->pdo->exec("ROLLBACK TO SAVEPOINT LEVEL". $this->transactionLevel);
            }
        }
        return false;
    }
    public function execute(string $sql, array $params): bool{
        return $this->doExecute($sql, $params);
    }
    public function execute2(string $sql, array $params): bool{
        return $this->doExecute($sql, $params);
    }
    public function execute3(string $sql, array $params): bool{
        return $this->doExecute($sql, $params);
    }
    public function fetchOne(string $sql, array $params): ?array{
        $stmt = $this->query($sql, $params);
        return $this->doFetchOne($stmt);
    }
    public function fetchOne2(string $sql, array $params): ?array{
        $stmt = $this->query2($sql, $params);
        return $this->doFetchOne($stmt);
    }
    public function fetchOne3(string $sql, array $params): ?array{
        $stmt = $this->query3($sql, $params);
        return $this->doFetchOne($stmt);
    }
    public function fetchMany(string $sql, array $params, int $seekBegin, int $fetchNums): array{
        switch ($this->params["driver"]){
            case "mysql":
                $sql .= " LIMIT ". strval($seekBegin). ",". strval($fetchNums);
                break;
        }
        $stmt = $this->query($sql, $params);
        return $this->doFetchMany($stmt);
    }
    public function fetchMany2(string $sql, array $params, int $seekBegin, int $fetchNums): array{
        switch ($this->params["driver"]){
            case "mysql":
                $sql .= " LIMIT ". strval($seekBegin). ",". strval($fetchNums);
                break;
        }
        $stmt = $this->query2($sql, $params);
        return $this->doFetchMany($stmt);
    }
    public function fetchMany3(string $sql, array $params, int $seekBegin, int $fetchNums): array{
        switch ($this->params["driver"]){
            case "mysql":
                $sql .= " LIMIT ". strval($seekBegin). ",". strval($fetchNums);
                break;
        }
        $stmt = $this->query3($sql, $params);
        return $this->doFetchMany($stmt);
    }
    public function fetchAll(string $sql, array $params): array{
        $stmt = $this->query($sql, $params);
        return $this->doFetchAll($stmt);
    }
    public function fetchAll2(string $sql, array $params): array{
        $stmt = $this->query2($sql, $params);
        return $this->doFetchAll($stmt);
    }
    public function fetchAll3(string $sql, array $params): array{
        $stmt = $this->query3($sql, $params);
        return $this->doFetchAll($stmt);
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        switch ($this->params["driver"]){
            case "mysql":
                $lastInsertIdRow = $this->fetchOne3("select last_insert_id() as id", []);
                if($lastInsertIdRow){
                    return $lastInsertIdRow["id"];
                }
                return null;
        }
        return null;
    }
    public function makePagination(int $total, int $percentpage, int $currentpage, array $options=null): array{
        if($options === null) $options = [];
        $totalpage = ceil($total/$percentpage);
        if($currentpage > $totalpage) $currentpage = $totalpage;
        if($currentpage < 1) $currentpage = 1;
        $links = [
            "first"=>1,
            "previous"=>(($totalpage > 0 && $currentpage > 1) ? $currentpage-1 : 1),
            "next"=>(($totalpage > 0 && $currentpage < $totalpage) ? $currentpage+1 : $totalpage),
            "last"=>$totalpage
        ];
        $seekBegin = ($currentpage-1)*$percentpage;
        $fetchNums = $percentpage;
        if(($seekBegin+$fetchNums) > $total) $fetchNums = $total-$seekBegin;
        $from = ($currentpage-1)*$percentpage+1;
        $to = $currentpage*$percentpage;
        if($to > $total) $to = $total;
        $pagination = [
            "total"=>$total,
            "percentpage"=>$percentpage,
            "totalpage"=>$totalpage,
            "currentpage"=>$currentpage,
            "seekbegin"=>$seekBegin,
            "fetchnums"=>$fetchNums,
            "from"=>$from,
            "to"=>$to,
            "links"=>$links,
        ];
        if(isset($options["teamlinksLength"]) && $options["teamlinksLength"] > 0){
            $teamlinksFrom = $currentpage-$options["teamlinksLength"]/2;
            if($teamlinksFrom < 1) $teamlinksFrom = 1;
            $teamlinksTo = $currentpage+$options["teamlinksLength"]/2;
            if($teamlinksTo > $totalpage) $teamlinksTo = $totalpage;
            $pagination["teamlinks"] = [];
            for($i=$teamlinksFrom;$i<=$teamlinksTo;$i++){
                $pagination["teamlinks"][] = $i;
            }
        }
        return $pagination;
    }
    public function getPDO(): \PDO{
        $this->readyTest();
        return $this->pdo;
    }
}