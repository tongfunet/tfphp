<?php

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

class tfdo{
    private tfphp $tfphp;
    private array $params;
    private bool $ready;
    private \PDO $pdo;
    public function __construct(tfphp $A, array $F){
        $this->tfphp = $A;
        $this->params = $F;
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
                    $A9 = "mysql:host=". $this->params["host"]. ";port=". $this->params["port"]. ";dbname=". $this->params["database"]. ";charset=". $this->params["charset"];
                    $this->pdo = new \PDO($A9, $this->params["username"], $this->params["password"]);
                    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $this->pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
                    $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
                    break;
            }
        }
    }
    private function query(string $AD, array $F): \PDOStatement{
        $this->readyTest();
        $B1 = $this->pdo->prepare($AD);
        foreach ($F as $param){
            $B1->bindParam($param["name"], $param["value"], $param["type"]);
        }
        $B1->execute();
        return $B1;
    }
    private function query2(string $AD, array $F): \PDOStatement{
        $this->readyTest();
        $B2 = count($F);
        $B3 = 0 ;
        $B4 = [] ;
        while(preg_match("/\?/", $AD, $rg, PREG_OFFSET_CAPTURE)){
            if(($B3+1) > $B2){
                throw new \Exception("too few arguments, ". strval($B3+1). " are needed and ". strval($B2). " are given");
            }
            $B7 = ":f". strval($B3);
            $AD = substr($AD, 0, $rg[0][1]). $B7. substr($AD, $rg[0][1]+strlen($rg[0][0])) ;
            $B4[] = [
                "name"=>$B7,
                "type"=>\PDO::PARAM_STR,
                "value"=>$F[$B3]
            ];
            $B3 ++;
        }
        if($B3 < $B2){
            throw new \Exception("too many arguments, ". strval($B3). " are needed and ". strval($B2). " are given");
        }
        return $this->query($AD, $B4);
    }
    private function query3(string $AD, array $F): \PDOStatement{
        $this->readyTest();
        $B2 = count($F);
        $B3 = 0 ;
        $B4 = [] ;
        while(preg_match("/\@(int|str)/", $AD, $rg, PREG_OFFSET_CAPTURE)){
            if(($B3+1) > $B2){
                throw new \Exception("too few arguments, ". strval($B3+1). " are needed and ". strval($B2). " are given");
            }
            $B7 = ":f". strval($B3);
            if($rg[1][0] == "int") $B9 = \PDO::PARAM_INT;
            else $B9 = \PDO::PARAM_STR;
            $AD = substr($AD, 0, $rg[0][1]). $B7. substr($AD, $rg[0][1]+strlen($rg[0][0])) ;
            $B4[] = [
                "name"=>$B7,
                "type"=>$B9,
                "value"=>$F[$B3]
            ];
            $B3 ++;
        }
        if($B3 < $B2){
            throw new \Exception("too many arguments, ". strval($B3). " are needed and ". strval($B2). " are given");
        }
        return $this->query($AD, $B4);
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
    public function execute(string $AD, array $F): bool{
        $this->query($AD, $F);
        return true;
    }
    public function execute2(string $AD, array $F): bool{
        $this->query2($AD, $F);
        return true;
    }
    public function execute3(string $AD, array $F): bool{
        $this->query3($AD, $F);
        return true;
    }
    public function fetchOne(string $AD, array $F): ?array{
        $B1 = $this->query($AD, $F);
        $BD = $B1->fetch(\PDO::FETCH_ASSOC) ;
        if($BD === false){
            return null;
        }
        return $BD;
    }
    public function fetchOne2(string $AD, array $F): ?array{
        $B1 = $this->query2($AD, $F);
        $BD = $B1->fetch(\PDO::FETCH_ASSOC) ;
        if($BD === false){
            return null;
        }
        return $BD;
    }
    public function fetchOne3(string $AD, array $F): ?array{
        $B1 = $this->query3($AD, $F);
        $BD = $B1->fetch(\PDO::FETCH_ASSOC) ;
        if($BD === false){
            return null;
        }
        return $BD;
    }
    public function fetchMany(string $AD, array $F, int $C1, int $C4): ?array{
        switch ($this->params["driver"]){
            default:
                $AD .= " LIMIT ". strval($C1). ",". strval($C4);
        }
        $B1 = $this->query($AD, $F);
        $C9 = $B1->fetchAll(\PDO::FETCH_ASSOC) ;
        if($C9 === false || count($C9) == 0){
            return null;
        }
        return $C9;
    }
    public function fetchMany2(string $AD, array $F, int $C1, int $C4): ?array{
        switch ($this->params["driver"]){
            default:
                $AD .= " LIMIT ". strval($C1). ",". strval($C4);
        }
        $B1 = $this->query2($AD, $F);
        $C9 = $B1->fetchAll(\PDO::FETCH_ASSOC) ;
        if($C9 === false || count($C9) == 0){
            return null;
        }
        return $C9;
    }
    public function fetchMany3(string $AD, array $F, int $C1, int $C4): ?array{
        switch ($this->params["driver"]){
            default:
                $AD .= " LIMIT ". strval($C1). ",". strval($C4);
        }
        $B1 = $this->query3($AD, $F);
        $C9 = $B1->fetchAll(\PDO::FETCH_ASSOC) ;
        if($C9 === false || count($C9) == 0){
            return null;
        }
        return $C9;
    }
    public function fetchAll(string $AD, array $F): ?array{
        $B1 = $this->query($AD, $F);
        $C9 = $B1->fetchAll(\PDO::FETCH_ASSOC) ;
        if($C9 === false || count($C9) == 0){
            return null;
        }
        return $C9;
    }
    public function fetchAll2(string $AD, array $F): ?array{
        $B1 = $this->query2($AD, $F);
        $C9 = $B1->fetchAll(\PDO::FETCH_ASSOC) ;
        if($C9 === false || count($C9) == 0){
            return null;
        }
        return $C9;
    }
    public function fetchAll3(string $AD, array $F): ?array{
        $B1 = $this->query3($AD, $F);
        $C9 = $B1->fetchAll(\PDO::FETCH_ASSOC) ;
        if($C9 === false || count($C9) == 0){
            return null;
        }
        return $C9;
    }
    public function makePagination(int $CC, int $CD, int $D1): array{
        $D2 = ceil($CC/$CD);
        if($D1 > $D2) $D1 = $D2;
        if($D1 < 1) $D1 = 1;
        $D4 = [
            "first"=>(($D2 > 0 && $D1 != 1) ?1 : 0),
            "previous"=>(($D2 > 0 && $D1 > 1) ? $D1-1 : 0),
            "next"=>(($D2 > 0 && $D1 < $D2) ? $D1+1 : 0),
            "last"=>(($D2 > 0 && $D1 != $D2) ? $D2 : 0),
        ] ;
        return [
            "total"=>$CC,
            "percentpage"=>$CD,
            "totalpage"=>$D2,
            "currentpage"=>$D1,
            "links"=>$D4,
        ];
    }
    public function getPDO(): \PDO{
        $this->readyTest();
        return $this->pdo;
    }
}