<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;
use tfphp\framework\database\tfdo;

class tfdaoSingle extends tfdao {
    protected tfdo $tfdo;
    protected string $tableName;
    protected array $tableFields;
    protected array $tableConstraints;
    protected ?string $autoIncrementField;
    protected ?array $lastInsertAutoIncrementData;
    public function __construct(tfphp $tfphp, array $tableParams){
        parent::__construct($tfphp);
        $this->tfdo = $this->tfphp->getDataSource($tableParams["dataSource"]);
        if(!isset($tableParams["name"])){
            throw new \Exception("param 'name' is missing");
        }
        if(!isset($tableParams["fields"])){
            throw new \Exception("param 'fields' is missing");
        }
        else if(!is_array($tableParams["fields"]) || count($tableParams["fields"]) == 0){
            throw new \Exception("param 'fields' is invalid");
        }
        $this->tableName = $tableParams["name"];
        $this->tableFields = $tableParams["fields"];
        $this->tableConstraints = $tableParams["constraints"];
        $this->autoIncrementField = $tableParams["autoIncrementField"];
        $this->lastInsertAutoIncrementData = null;
    }
    private function makeParams(array $query, array $options=null): array{
        $params = [];
        $fnn = intval($options["startFieldNameNumber"]);
        foreach ($this->tableFields as $fieldName => $tableField){
            if(isset($query[$fieldName])){
                $params[$fieldName] = [
                    "name"=>":f". strval($fnn),
                    "type"=>$tableField["type"],
                    "value"=>$query[$fieldName],
                ];
                $fnn ++;
            }
        }
        return $params;
    }
    private function makeSelectParams(array $query, string $constraintName=null): array{
        $queryParams = $this->makeParams($query);
        if(count($queryParams) == 0){
            throw new \Exception("no condition items for select");
        }
        if($constraintName){
            if(!isset($this->tableConstraints[$constraintName])){
                throw new \Exception("constraint '". $constraintName. "' of table '". $this->tableName. "' for select is invalid");
            }
        }
        $sql = "SELECT * FROM ". $this->tableName. " WHERE ";
        $params = [];
        if($constraintName){
            foreach ($this->tableConstraints[$constraintName] as $fieldName){
                if(!isset($queryParams[$fieldName])){
                    throw new \Exception("field '". $fieldName. "' of constraint '". $constraintName. "' of table '". $this->tableName. "' for select is invalid");
                }
                $sql .= $fieldName. " = ". $queryParams[$fieldName]["name"]. " AND ";
                $params[] = $queryParams[$fieldName];
            }
        }
        else{
            foreach ($queryParams as $fieldName => $queryParam){
                $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
                $params[] = $queryParam;
            }
        }
        $sql = substr($sql, 0, -5);
//        var_dump($sql, $params);
        return [$sql, $params];
    }
    public function select(array $query, string $constraintName=null): ?array{
        list($sql, $params) = $this->makeSelectParams($query, $constraintName);
        return $this->tfdo->fetchOne($sql, $params);
    }
    public function selectMany(array $query, string $constraintName=null, int $seekBegin=0, int $fetchNums=10): ?array{
        list($sql, $params) = $this->makeSelectParams($query, $constraintName);
        return $this->tfdo->fetchMany($sql, $params, $seekBegin, $fetchNums);
    }
    public function selectAll(array $query, string $constraintName=null): ?array{
        list($sql, $params) = $this->makeSelectParams($query, $constraintName);
        return $this->tfdo->fetchAll($sql, $params);
    }
    public function insert(array $data, array $options=null): bool{
        $dataParams = $this->makeParams($data);
        if(count($dataParams) == 0){
            throw new \Exception("no insert items for insert");
        }
        if(isset($options["checkConstraints"]) && $options["checkConstraints"]){
            foreach ($this->tableConstraints as $tableConstraintName => $tableConstraint){
                $dupData = null;
                try{
                    $dupData = $this->select($data, $tableConstraintName);
                }
                catch(\Exception $e){ }
                if($dupData){
                    throw new \Exception("data of constraint '". $tableConstraintName. "' of table '". $this->tableName. "' for insert is duplicate");
                }
            }
        }
        $fields = $names = [];
        foreach ($dataParams as $fieldName => $dataParam){
            $fields[] = $fieldName;
            $names[] = $dataParam["name"];
        }
        $sql = "INSERT INTO ". $this->tableName. " (". implode(",", $fields). ") VALUES (". implode(",", $names). ")";
//        var_dump($sql, $dataParams);
        return $this->tfdo->execute($sql, $dataParams);
    }
    public function update(array $query, array $data, string $constraintName=null, array $options=null): bool{
        $queryParams = $this->makeParams($query);
        $dataParams = $this->makeParams($data, [
            "startFieldNameNumber"=>count($queryParams)
        ]);
        if(count($queryParams) == 0){
            throw new \Exception("no condition items for update");
        }
        if(count($dataParams) == 0){
            throw new \Exception("no update items for update");
        }
        if($constraintName){
            if(!isset($this->tableConstraints[$constraintName])){
                throw new \Exception("constraint '". $constraintName. "' of table '". $this->tableName. "' for update is invalid");
            }
        }
        if(isset($options["checkConstraints"]) && $options["checkConstraints"]){
            $curData = $this->select($query, $constraintName);
            if($curData === null){
                throw new \Exception("data of constraint '". $constraintName. "' of table '". $this->tableName. "' for update is not found");
            }
            foreach ($this->tableConstraints as $tableConstraintName => $tableConstraint){
                $dupData = null;
                try{
                    $dupData = $this->select($data, $tableConstraintName);
                }
                catch(\Exception $e){ }
                if($dupData && serialize($dupData) != serialize($curData)){
                    throw new \Exception("data of constraint '". $tableConstraintName. "' of table '". $this->tableName. "' for update is duplicate");
                }
            }
        }
        $sql = "UPDATE ". $this->tableName. " SET ";
        $params = [];
        foreach ($dataParams as $fieldName => $dataParam){
            $sql .= $fieldName. " = ". $dataParam["name"]. ", ";
            $params[] = $dataParam;
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE ";
        if($constraintName){
            foreach ($this->tableConstraints[$constraintName] as $fieldName){
                if(!isset($queryParams[$fieldName])){
                    throw new \Exception("field '". $fieldName. "' of constraint '". $constraintName. "' of table '". $this->tableName. "' for update is invalid");
                }
                $sql .= $fieldName. " = ". $queryParams[$fieldName]["name"]. " AND ";
                $params[] = $queryParams[$fieldName];
            }
        }
        else{
            foreach ($queryParams as $fieldName => $queryParam){
                $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
                $params[] = $queryParam;
            }
        }
        $sql = substr($sql, 0, -5);
//        var_dump($sql, $params);
        return $this->tfdo->execute($sql, $params);
    }
    public function delete(array $query, string $constraintName=null): bool{
        $queryParams = $this->makeParams($query);
        if(count($queryParams) == 0){
            throw new \Exception("no condition items for delete");
        }
        if($constraintName){
            if(!isset($this->tableConstraints[$constraintName])){
                throw new \Exception("constraint '". $constraintName. "' of table '". $this->tableName. "' for delete is invalid");
            }
        }
        $sql = "DELETE FROM ". $this->tableName. " WHERE ";
        if($constraintName){
            foreach ($this->tableConstraints[$constraintName] as $fieldName){
                if(!isset($queryParams[$fieldName])){
                    throw new \Exception("field '". $fieldName. "' of constraint '". $constraintName. "' of table '". $this->tableName. "' for delete is invalid");
                }
                $sql .= $fieldName. " = ". $queryParams[$fieldName]["name"]. " AND ";
                $params[] = $queryParams[$fieldName];
            }
        }
        else{
            foreach ($queryParams as $fieldName => $queryParam){
                $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
                $params[] = $queryParam;
            }
        }
        $sql = substr($sql, 0, -5);
//        var_dump($sql, $params);
        return $this->tfdo->execute($sql, $params);
    }
    public function getAutoIncrementField(): ?string{
        return $this->autoIncrementField;
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        if($this->autoIncrementField){
            $row = $this->tfdo->fetchOne("SELECT last_insert_id() AS id", []);
            if($row){
                return $row["id"];
            }
        }
        return null;
    }
    public function getLastInsert(): ?array{
        if($this->autoIncrementField){
            $row = $this->select([
                $this->autoIncrementField=>$this->getLastInsertAutoIncrementValue()
            ]);
            if($row){
                return $row;
            }
        }
        return null;
    }
    public function getTFDO(): tfdo{
        return $this->tfdo;
    }
}