<?php

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;

class tfdaoSingle extends tfdao{
    protected ?tfdo $tfdo;
    protected string $tableName;
    protected array $tableFields;
    protected array $tableConstraints;
    protected ?string $autoIncrementField;
    protected ?array $lastInsertAutoIncrementData;
    public function __construct(tfphp $B4, array $BA){
        parent::__construct($B4);
        if(!isset($BA["name"])){
            throw new \Exception("param 'name' is missing");
        }
        if(!isset($BA["fields"])){
            throw new \Exception("param 'fields' is missing");
        }
        else if(!is_array($BA["fields"]) || count($BA["fields"]) == 0){
            throw new \Exception("param 'fields' is invalid");
        }
        $this->tfdo = $this->tfphp->getDataSource($BA["dataSource"]);
        $this->tableName = $BA["name"];
        $this->tableFields = $BA["fields"];
        $this->tableConstraints = $BA["constraints"];
        $this->autoIncrementField = $BA["autoIncrementField"];
        $this->lastInsertAutoIncrementData = null;
    }
    private function makeQueryParams(array $BC, array $BE=null): array{
        $C1 = [];
        $C2 = intval($BE["startFieldNameNumber"]) ;
        foreach ($this->tableFields as $C5 => $tableField){
            if(isset($BC[$C5])){
                $C1[$C5] = [
                    "name"=>":f". strval($C2),
                    "type"=>$tableField["type"],
                    "value"=>$BC[$C5],
                ];
                $C2 ++;
            }
        }
        return $C1;
    }
    private function makeConstraintParams(array $C1, string $C8, array $BE=null): array{
        $CE = [];
        $C2 = 0 ;
        $CF = $this->tableConstraints[$C8] ;
        foreach ($this->tableFields as $C5 => $tableField){
            if(in_array($C5, $CF)){
                $CE[$C5] = [
                    "name"=>":f". strval($C2),
                    "type"=>$tableField["type"],
                    "value"=>$C1[$C2],
                ];
                $C2 ++;
            }
        }
        return $CE;
    }
    private function makeSelectParams(array $BC): array{
        $D5 = $this->makeQueryParams($BC);
        if(count($D5) == 0){
            throw new \Exception("no condition items for select");
        }
        $DA = "SELECT * FROM ". $this->tableName. " WHERE " ;
        $C1 = [] ;
        foreach ($D5 as $C5 => $queryParam){
            $DA .= $C5. " = ". $queryParam["name"]. " AND ";
            $C1[] = $queryParam;
        }
        $DA = substr($DA, 0, -5) ;
        return [$DA, $C1];
    }
    private function makeConstraintSelectParams(array $C1, string $C8): array{
        if(!isset($this->tableConstraints[$C8])){
            throw new \Exception("constraint '". $C8. "' of table '". $this->tableName. "' for select is invalid");
        }
        $D5 = $this->makeConstraintParams($C1, $C8);
        if(count($D5) == 0){
            throw new \Exception("no condition items for select");
        }
        $DA = "SELECT * FROM ". $this->tableName. " WHERE " ;
        $CE = [] ;
        foreach ($D5 as $C5 => $queryParam){
            $DA .= $C5. " = ". $queryParam["name"]. " AND ";
            $CE[] = $queryParam;
        }
        $DA = substr($DA, 0, -5) ;
        return [$DA, $CE];
    }
    public function select(array $BC): ?array{
        list($DA, $C1) = $this->makeSelectParams($BC);
        return $this->tfdo->fetchOne($DA, $C1);
    }
    public function constraintSelect(array $C1, string $C8="default"): ?array{
        list($DA, $CE) = $this->makeConstraintSelectParams($C1, $C8);
        return $this->tfdo->fetchOne($DA, $CE);
    }
    public function keySelect(array $C1, string $C8="default"): ?array{
        return $this->constraintSelect($C1, $C8);
    }
    public function selectMany(array $BC, int $DF=0, int $E3=10): ?array{
        list($DA, $C1) = $this->makeSelectParams($BC);
        return $this->tfdo->fetchMany($DA, $C1, $DF, $E3);
    }
    public function constraintSelectMany(array $C1, string $C8="default", int $DF=0, int $E3=10): ?array{
        list($DA, $CE) = $this->makeConstraintSelectParams($C1, $C8);
        return $this->tfdo->fetchMany($DA, $CE, $DF, $E3);
    }
    public function keySelectMany(array $C1, string $C8="default", int $DF=0, int $E3=10): ?array{
        return $this->constraintSelectMany($C1, $C8, $DF, $E3);
    }
    public function selectAll(array $BC): ?array{
        list($DA, $C1) = $this->makeSelectParams($BC);
        return $this->tfdo->fetchAll($DA, $C1);
    }
    public function constraintSelectAll(array $C1, string $C8="default"): ?array{
        list($DA, $CE) = $this->makeConstraintSelectParams($C1, $C8);
        return $this->tfdo->fetchAll($DA, $CE);
    }
    public function keySelectAll(array $C1, string $C8="default"): ?array{
        return $this->constraintSelectAll($C1, $C8);
    }
    public function insert(array $E9, array $BE=null): bool{
        $EB = $this->makeQueryParams($E9);
        if(count($EB) == 0){
            throw new \Exception("no insert items for insert");
        }
        if(isset($BE["checkConstraints"]) && $BE["checkConstraints"]){
            foreach ($this->tableConstraints as $EE => $CF){
                $F1 = null;
                try{
                    $CE = [];
                    foreach ($CF as $C5) $CE[] = $E9[$C5];
                    $F1 = $this->constraintSelect($CE, $EE);
                }
                catch(\Exception $e){ }
                if($F1){
                    throw new \Exception("data of constraint '". $EE. "' of table '". $this->tableName. "' for insert is duplicate");
                }
            }
        }
        $F5 = $FA = [] ;
        foreach ($EB as $C5 => $dataParam){
            $F5[] = $C5;
            $FA[] = $dataParam["name"];
        }
        $DA = "INSERT INTO ". $this->tableName. " (". implode(",", $F5). ") VALUES (". implode(",", $FA). ")" ;
        return $this->tfdo->execute($DA, $EB);
    }
    public function update(array $BC, array $E9, array $BE=null): bool{
        $D5 = $this->makeQueryParams($BC);
        $EB = $this->makeQueryParams($E9, ["startFieldNameNumber"=>count($D5)]) ;
        if(count($D5) == 0){
            throw new \Exception("no condition items for update");
        }
        if(count($EB) == 0){
            throw new \Exception("no update items for update");
        }
        if(isset($BE["checkConstraints"]) && $BE["checkConstraints"]){
            $FE = $this->select($BC);
            if($FE === null){
                throw new \Exception("data of table '". $this->tableName. "' for update is not found");
            }
            foreach ($this->tableConstraints as $EE => $CF){
                $F1 = null;
                try{
                    $CE = [];
                    foreach ($CF as $C5) $CE[] = $E9[$C5];
                    $F1 = $this->constraintSelect($CE, $EE);
                }
                catch(\Exception $e){ }
                if($F1 && serialize($F1) != serialize($FE)){
                    throw new \Exception("data of constraint '". $EE. "' of table '". $this->tableName. "' for update is duplicate");
                }
            }
        }
        $DA = "UPDATE ". $this->tableName. " SET " ;
        $C1 = [] ;
        foreach ($EB as $C5 => $dataParam){
            $DA .= $C5. " = ". $dataParam["name"]. ", ";
            $C1[] = $dataParam;
        }
        $DA = substr($DA, 0, -2) ;
        $DA .= " WHERE ";
        foreach ($D5 as $C5 => $queryParam){
            $DA .= $C5. " = ". $queryParam["name"]. " AND ";
            $C1[] = $queryParam;
        }
        $DA = substr($DA, 0, -5) ;
        return $this->tfdo->execute($DA, $C1);
    }
    public function constraintUpdate(array $C1, array $E9, string $C8="default", array $BE=null): bool{
        $D5 = $this->makeConstraintParams($C1, $C8);
        $BC = [] ;
        foreach ($D5 as $A00 => $queryParam) $BC[$A00] = $queryParam["value"];
        return $this->update($BC, $E9, $BE);
    }
    public function keyUpdate(array $C1, array $E9, string $C8="default", array $BE=null): bool{
        return $this->constraintUpdate($C1, $E9, $C8, $BE);
    }
    public function delete(array $BC): bool{
        $D5 = $this->makeQueryParams($BC);
        if(count($D5) == 0){
            throw new \Exception("no condition items for delete");
        }
        $DA = "DELETE FROM ". $this->tableName. " WHERE " ;
        foreach ($D5 as $C5 => $queryParam){
            $DA .= $C5. " = ". $queryParam["name"]. " AND ";
            $C1[] = $queryParam;
        }
        $DA = substr($DA, 0, -5) ;
        return $this->tfdo->execute($DA, $C1);
    }
    public function constraintDelete(array $C1, string $C8="default"): bool{
        $D5 = $this->makeConstraintParams($C1, $C8);
        $BC = [] ;
        foreach ($D5 as $A00 => $queryParam) $BC[$A00] = $queryParam["value"];
        return $this->delete($BC);
    }
    public function keyDelete(array $C1, string $C8="default"): bool{
        return $this->constraintDelete($C1, $C8);
    }
    public function getAutoIncrementField(): ?string{
        return $this->autoIncrementField;
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        if($this->autoIncrementField){
            $A05 = $this->tfdo->fetchOne("SELECT last_insert_id() AS id", []);
            if($A05){
                return $A05["id"];
            }
        }
        return null;
    }
    public function getLastInsert(): ?array{
        if($this->autoIncrementField){
            $A05 = $this->select([$this->autoIncrementField=>$this->getLastInsertAutoIncrementValue()]);
            if($A05){
                return $A05;
            }
        }
        return null;
    }
    public function getTFDO(): tfdo{
        return $this->tfdo;
    }
}