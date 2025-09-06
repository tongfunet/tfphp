<?php 

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

/**
 * Class tfdo
 * @package tfphp\framework\database
 * @datetime 2025/7/17
 */
class tfdo{
    private tfphp $A;
    private array $E;
    private bool $A0;
    private int $A5;
    private \PDO $AB;
    public function __construct(tfphp $B0, array $B2){
        $this->A = $B0;
        $this->E = $B2;
        $this->A0 = false;
        $this->A5 = 0;
    }
    private function B7(){
        if(!$this->A0){
            $this->A0 = true;
            if(empty($this->E["driver"])) $this->E["driver"] = "mysql";
            switch ($this->E["driver"]){
                case "mysql":
                    if(empty($this->E["host"])) $this->E["host"] = "localhost";
                    if(empty($this->E["port"])) $this->E["port"] = 3306;
                    if(empty($this->E["database"])) $this->E["database"] = "";
                    if(empty($this->E["username"])) $this->E["username"] = "root";
                    if(empty($this->E["password"])) $this->E["password"] = "";
                    if(empty($this->E["charset"])) $this->E["charset"] = "utf8mb4";
                    $BA = "mysql:host=". $this->E["host"]. ";port=". $this->E["port"]. ";dbname=". $this->E["database"]. ";charset=". $this->E["charset"];
                    $this->AB = new \PDO($BA, $this->E["username"], $this->E["password"]);
                    $this->AB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $this->AB->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
                    $this->AB->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
                    break;
                default:
                    throw new \Exception("invalid type ". $this->E["driver"]. " for TFDO");
            }
        }
    }
    private function BB(string $C0, array $B2): \PDOStatement{
        $this->B7();
        $C2 = $this->AB->prepare($C0);
        foreach ($B2 as $C5){
            $C2->bindParam($C5["name"], $C5["value"], $C5["type"]);
        }
        $C2->execute();
        return $C2;
    }
    private function C9(string $C0, array $B2): \PDOStatement{
        $this->B7();
        $CF = count($B2);
        $D5 = [];
        $D9 = 0;
        while(preg_match("/\?/", $C0, $DE, PREG_OFFSET_CAPTURE)){
            if(($D9+1) > $CF){
                throw new \Exception("too few arguments, ". strval($D9+1). " are needed and ". strval($CF). " are given", 666031);
            }
            $DF = ":f". strval($D9);
            $C0 = substr($C0, 0, $DE[0][1]). $DF. substr($C0, $DE[0][1]+strlen($DE[0][0]));
            $D5[] = [
                "name"=>$DF,
                "type"=>\PDO::PARAM_STR,
                "value"=>$B2[$D9]
            ];
            $D9 ++;
        }
        if($D9 < $CF){
            throw new \Exception("too many arguments, ". strval($D9). " are needed and ". strval($CF). " are given", 666032);
        }
        return $this->BB($C0, $D5);
    }
    private function E5(string $C0, array $B2): \PDOStatement{
        $this->B7();
        $CF = count($B2);
        $D5 = [];
        $D9 = 0;
        while(preg_match("/\@(int|str)/", $C0, $DE, PREG_OFFSET_CAPTURE)){
            if(($D9+1) > $CF){
                throw new \Exception("too few arguments, ". strval($D9+1). " are needed and ". strval($CF). " are given", 666031);
            }
            $DF = ":f". strval($D9);
            if($DE[1][0] == "int") $E6 = \PDO::PARAM_INT;
            else $E6 = \PDO::PARAM_STR;
            $C0 = substr($C0, 0, $DE[0][1]). $DF. substr($C0, $DE[0][1]+strlen($DE[0][0]));
            $D5[] = [
                "name"=>$DF,
                "type"=>$E6,
                "value"=>$B2[$D9]
            ];
            $D9 ++;
        }
        if($D9 < $CF){
            throw new \Exception("too many arguments, ". strval($D9). " are needed and ". strval($CF). " are given", 666032);
        }
        return $this->BB($C0, $D5);
    }
    private function EA(string $C0, array $B2): bool{
        $this->BB($C0, $B2);
        return true;
    }
    private function EF(\PDOStatement $C2): ?array{
        $F0 = $C2->fetch(\PDO::FETCH_ASSOC);
        if($F0 === false){
            return null;
        }
        return $F0;
    }
    private function F3(\PDOStatement $C2): array{
        $F8 = $C2->fetchAll(\PDO::FETCH_ASSOC);
        if($F8 === false || count($F8) == 0){
            return [];
        }
        return $F8;
    }
    private function FD(\PDOStatement $C2): array{
        $F8 = $C2->fetchAll(\PDO::FETCH_ASSOC);
        if($F8 === false || count($F8) == 0){
            return [];
        }
        return $F8;
    }
    public function beginTransaction(): bool{
        $this->B7();
        if($this->A5 == 0){
            $FE = $this->AB->beginTransaction();
        }
        else{
            $FE = $this->AB->exec("SAVEPOINT LEVEL". $this->A5);
        }
        $this->A5 ++;
        return $FE;
    }
    public function commit(): bool{
        $this->B7();
        if($this->A5 > 0){
            $this->A5 --;
            if($this->A5 == 0){
                return $this->AB->commit();
            }
        }
        return false;
    }
    public function rollback(): bool{
        $this->B7();
        if($this->A5 > 0){
            $this->A5 --;
            if($this->A5 == 0){
                return $this->AB->rollBack();
            }
            else{
                return $this->AB->exec("ROLLBACK TO SAVEPOINT LEVEL". $this->A5);
            }
        }
        return false;
    }
    public function execute(string $C0, array $B2): bool{
        return $this->EA($C0, $B2);
    }
    public function execute2(string $C0, array $B2): bool{
        return $this->EA($C0, $B2);
    }
    public function execute3(string $C0, array $B2): bool{
        return $this->EA($C0, $B2);
    }
    public function fetchOne(string $C0, array $B2): ?array{
        $C2 = $this->BB($C0, $B2);
        return $this->EF($C2);
    }
    public function fetchOne2(string $C0, array $B2): ?array{
        $C2 = $this->C9($C0, $B2);
        return $this->EF($C2);
    }
    public function fetchOne3(string $C0, array $B2): ?array{
        $C2 = $this->E5($C0, $B2);
        return $this->EF($C2);
    }
    public function fetchMany(string $C0, array $B2, int $A01, int $A02): array{
        switch ($this->E["driver"]){
            case "mysql":
                $C0 .= " LIMIT ". strval($A01). ",". strval($A02);
                break;
        }
        $C2 = $this->BB($C0, $B2);
        return $this->F3($C2);
    }
    public function fetchMany2(string $C0, array $B2, int $A01, int $A02): array{
        switch ($this->E["driver"]){
            case "mysql":
                $C0 .= " LIMIT ". strval($A01). ",". strval($A02);
                break;
        }
        $C2 = $this->C9($C0, $B2);
        return $this->F3($C2);
    }
    public function fetchMany3(string $C0, array $B2, int $A01, int $A02): array{
        switch ($this->E["driver"]){
            case "mysql":
                $C0 .= " LIMIT ". strval($A01). ",". strval($A02);
                break;
        }
        $C2 = $this->E5($C0, $B2);
        return $this->F3($C2);
    }
    public function fetchAll(string $C0, array $B2): array{
        $C2 = $this->BB($C0, $B2);
        return $this->FD($C2);
    }
    public function fetchAll2(string $C0, array $B2): array{
        $C2 = $this->C9($C0, $B2);
        return $this->FD($C2);
    }
    public function fetchAll3(string $C0, array $B2): array{
        $C2 = $this->E5($C0, $B2);
        return $this->FD($C2);
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        switch ($this->E["driver"]){
            case "mysql":
                $A04 = $this->fetchOne3("select last_insert_id() as id", []);
                if($A04){
                    return $A04["id"];
                }
                return null;
        }
        return null;
    }
    public function makePagination(int $A07, int $A0D, int $A0E, array $A10=null): array{
        if($A10 === null) $A10 = [];
        $A14 = ceil($A07/$A0D);
        if($A0E > $A14) $A0E = $A14;
        if($A0E < 1) $A0E = 1;
        $A19 = [
            "first"=>1,
            "previous"=>(($A14 > 0 && $A0E > 1) ? $A0E-1 : 1),
            "next"=>(($A14 > 0 && $A0E < $A14) ? $A0E+1 : $A14),
            "last"=>$A14
        ];
        $A01 = ($A0E-1)*$A0D;
        $A02 = $A0D;
        if(($A01+$A02) > $A07) $A02 = $A07-$A01;
        $A1D = ($A0E-1)*$A0D+1;
        $A22 = $A0E*$A0D;
        if($A22 > $A07) $A22 = $A07;
        $A25 = [
            "total"=>$A07,
            "percentpage"=>$A0D,
            "totalpage"=>$A14,
            "currentpage"=>$A0E,
            "seekbegin"=>$A01,
            "fetchnums"=>$A02,
            "from"=>$A1D,
            "to"=>$A22,
            "links"=>$A19,
        ];
        if(isset($A10["teamlinksLength"]) && $A10["teamlinksLength"] > 0){
            $A28 = $A0E-$A10["teamlinksLength"]/2;
            if($A28 < 1) $A28 = 1;
            $A2A = $A0E+$A10["teamlinksLength"]/2;
            if($A2A > $A14) $A2A = $A14;
            $A25["teamlinks"] = [];
            for($D9=$A28;$D9<=$A2A;$D9++){
                $A25["teamlinks"][] = $D9;
            }
        }
        return $A25;
    }
    public function getPDO(): \PDO{
        $this->B7();
        return $this->AB;
    }
}