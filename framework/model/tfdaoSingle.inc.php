<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;

/**
 * Class tfdaoSingle
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoSingle extends tfdao {
    private array $methodChainingParams;
    protected ?tfdo $tfdo;
    protected string $tableName;
    protected array $tableFields;
    protected array $tableConstraints;
    protected ?string $autoIncrementField;
    public function __construct(tfphp $tfphp, array $tableParams){
        parent::__construct($tfphp);
        if(empty($tableParams["name"])){
            throw new \Exception("param 'name' is missing", 660301);
        }
        if(empty($tableParams["fields"])){
            throw new \Exception("param 'fields' is missing", 660302);
        }
        else if(!is_array($tableParams["fields"]) || count($tableParams["fields"]) == 0){
            throw new \Exception("param 'fields' is invalid", 660303);
        }
        $this->methodChainingParams = [];
        $this->tfdo = $this->tfphp->getDataSource($tableParams["dataSource"]);
        $this->tableName = $tableParams["name"];
        $this->tableFields = $tableParams["fields"];
        $this->tableConstraints = $tableParams["constraints"];
        $this->autoIncrementField = (isset($tableParams["autoIncrementField"])) ? $tableParams["autoIncrementField"] : null;
    }
    private function makeQueryParams(array $query, array $options=null): array{
        if($options === null) $options = [];
        $params = [];
        $fnn = (isset($options["startFieldNameNumber"])) ? intval($options["startFieldNameNumber"]) : 0;
        foreach ($this->tableFields as $fieldName => $tableField){
            if(isset($query[$fieldName])){
                $params[$fieldName] = [
                    "name"=>":f". strval($fnn),
                    "type"=>$tableField["type"],
                    "value"=>$query[$fieldName]
                ];
                $fnn ++;
            }
        }
        return $params;
    }
    private function makeConstraintQueryParams(array $params, string $constraintName, array $options=null): array{
        if($options === null) $options = [];
        $constraintParams = [];
        $fnn = 0;
        $tableConstraint = $this->tableConstraints[$constraintName];
        foreach ($tableConstraint as $fieldName){
            if(isset($this->tableFields[$fieldName])){
                $constraintParams[$fieldName] = [
                    "name"=>":f". strval($fnn),
                    "type"=>$this->tableFields[$fieldName]["type"],
                    "value"=>$params[$fnn]
                ];
                $fnn ++;
            }
        }
        return $constraintParams;
    }
    private function makeSelectParams(array $query, array $options=null): array{
        if($options === null) $options = [];
        $queryParams = $this->makeQueryParams($query);
        if(!isset($options["selectAll"]) || !is_bool($options["selectAll"])) $options["selectAll"] = false;
        if(!$options["selectAll"] && count($queryParams) == 0){
            throw new \Exception("no condition items for select", 660304);
        }
        if(!isset($options["selectFields"]) || !is_array($options["selectFields"])) $options["selectFields"] = [];
        $sql = "SELECT ". $this->makeSelectFields($options["selectFields"]). " FROM ". $this->tableName. " WHERE ";
        $params = [];
        foreach ($queryParams as $fieldName => $queryParam){
            $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
            $params[] = $queryParam;
        }
        $sql = substr($sql, 0, -5);
        if(isset($options["fieldOrders"]) && is_array($options["fieldOrders"])) $sql .= $this->makeFieldOrders($options["fieldOrders"]);
        return [$sql, $params];
    }
    private function makeConstraintSelectParams(array $params, string $constraintName, array $options=null): array{
        if($options === null) $options = [];
        if(!isset($this->tableConstraints[$constraintName])){
            throw new \Exception("constraint '". $constraintName. "' of table '". $this->tableName. "' for select is invalid", 660305);
        }
        $queryParams = $this->makeConstraintQueryParams($params, $constraintName);
        if(!isset($options["selectAll"]) || !is_bool($options["selectAll"])) $options["selectAll"] = false;
        if(!$options["selectAll"] && count($queryParams) == 0){
            throw new \Exception("no condition items for select", 660306);
        }
        if(!isset($options["selectFields"]) || !is_array($options["selectFields"])) $options["selectFields"] = [];
        $sql = "SELECT ". $this->makeSelectFields($options["selectFields"]). " FROM ". $this->tableName. " WHERE ";
        $constraintParams = [];
        foreach ($queryParams as $fieldName => $queryParam){
            $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
            $constraintParams[] = $queryParam;
        }
        $sql = substr($sql, 0, -5);
        return [$sql, $constraintParams];
    }
    private function makeSelectFields(array $selectFields): string{
        if(count($selectFields) > 0){
            foreach ($selectFields as $k => $selectField){
                if(!isset($this->tableFields[$selectField])) unset($selectFields[$k]);
            }
        }
        return (count($selectFields) > 0) ? implode(", ", $selectFields) : "*";
    }
    private function makeFieldOrders(array $fieldOrders): string{
        $sqlFieldOrders = [];
        if(count($fieldOrders) > 0){
            foreach ($fieldOrders as $field => $order){
                if(isset($this->tableFields[$field])) $sqlFieldOrders[] = $field. " ". $order;
            }
        }
        return (count($sqlFieldOrders) > 0) ? " ORDER BY ". implode(", ", $sqlFieldOrders) : "";
    }
    private function selectConstraint(array $params, string $constraintName=null, string $sqlWhere=null, int $seekBegin=-1, int $fetchNums=-1, array $options=null): ?array{
        if($options === null) $options = [];
        $options = array_merge($options, $this->methodChainingParams);
        $this->methodChainingParams = [];
        if($sqlWhere){
            if(!isset($options["selectFields"]) || !is_array($options["selectFields"])) $options["selectFields"] = [];
            $sql = "SELECT ". $this->makeSelectFields($options["selectFields"]). " FROM ". $this->tableName. " WHERE ". $sqlWhere;
            $sql = preg_replace("/\@(int|str)/", "", $sql);
            if(isset($options["fieldOrders"]) && is_array($options["fieldOrders"])) $sql .= $this->makeFieldOrders($options["fieldOrders"]);
            if($seekBegin >= 0 && $fetchNums >= 0){
                if($seekBegin == 0 && $fetchNums == 0){
                    return $this->tfdo->fetchAll($sql, $params);
                }
                else{
                    return $this->tfdo->fetchMany($sql, $params, $seekBegin, $fetchNums);
                }
            }
            return $this->tfdo->fetchOne($sql, $params);
        }
        if($constraintName) {
            list($sql, $sqlParams) = $this->makeConstraintSelectParams($params, $constraintName, $options);
        }
        else{
            list($sql, $sqlParams) = $this->makeSelectParams($params, $options);
        }
        if($seekBegin >= 0 && $fetchNums >= 0){
            if($seekBegin == 0 && $fetchNums == 0){
                return $this->tfdo->fetchAll($sql, $sqlParams);
            }
            else{
                return $this->tfdo->fetchMany($sql, $sqlParams, $seekBegin, $fetchNums);
            }
        }
        return $this->tfdo->fetchOne($sql, $sqlParams);
    }
    private function updateConstraint(array $data, array $params, string $constraintName=null, string $sqlWhere=null, array $options=null): bool{
        if($sqlWhere){
            $sqlParams = [];
            $sql = "UPDATE ". $this->tableName. " SET ";
            foreach ($data as $fieldName => $dataParam){
                $sql .= (is_numeric($dataParam["value"])) ? $fieldName. " = @int, " : $fieldName. " = @str, ";
                $params[] = $dataParam["value"];
            }
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE ";
            $sql .= $sqlWhere;
            foreach ($params as $param) $sqlParams[] = $param;
            return $this->tfdo->execute($sql, $params);
        }
        if($constraintName) {
            $queryParams = $this->makeConstraintQueryParams($params, $constraintName);
        }
        else{
            $queryParams = $this->makeSelectParams($params);
        }
        $query = [];
        foreach ($queryParams as $fileName => $queryParam) $query[$fileName] = $queryParam["value"];
        return $this->doUpdateConstraint($data, $query, $options);
    }
    private function doUpdateConstraint(array $data, array $query, array $options=null): bool{
        $queryParams = $this->makeQueryParams($query);
        $dataParams = $this->makeQueryParams($data, ["startFieldNameNumber"=>count($queryParams)]);
        if(count($queryParams) == 0){
            throw new \Exception("no condition items for update", 660309);
        }
        if(count($dataParams) == 0){
            throw new \Exception("no update items for update", 660310);
        }
        $params = [];
        $sql = "UPDATE ". $this->tableName. " SET ";
        foreach ($dataParams as $fieldName => $dataParam){
            $sql .= $fieldName. " = ". $dataParam["name"]. ", ";
            $params[] = $dataParam;
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE ";
        foreach ($queryParams as $fieldName => $queryParam){
            $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
            $params[] = $queryParam;
        }
        $sql = substr($sql, 0, -5);
        return $this->tfdo->execute($sql, $params);
    }
    private function deleteConstraint(array $params, string $constraintName=null, string $sqlWhere=null): bool{
        if($sqlWhere){
            $sqlParams = [];
            $sql = "DELETE FROM ". $this->tableName. " WHERE ";
            $sql .= $sqlWhere;
            foreach ($params as $param) $sqlParams[] = $param;
            return $this->tfdo->execute($sql, $sqlParams);
        }
        if($constraintName) {
            $queryParams = $this->makeConstraintQueryParams($params, $constraintName);
        }
        else{
            $queryParams = $this->makeSelectParams($params);
        }
        $query = [];
        foreach ($queryParams as $fileName => $queryParam) $query[$fileName] = $queryParam["value"];
        return $this->delete($query);
    }
    private function doDeleteConstraint(array $query): bool{
        $queryParams = $this->makeQueryParams($query);
        if(count($queryParams) == 0){
            throw new \Exception("no condition items for delete", 660315);
        }
        $params = [];
        $sql = "DELETE FROM ". $this->tableName. " WHERE ";
        foreach ($queryParams as $fieldName => $queryParam){
            $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
            $params[] = $queryParam;
        }
        $sql = substr($sql, 0, -5);
        return $this->tfdo->execute($sql, $params);
    }

    public function setFields(array $selectFields): tfdaoSingle{
        $this->methodChainingParams["selectFields"] = $selectFields;
        return $this;
    }
    public function setOrders(array $fieldOrders): tfdaoSingle{
        $this->methodChainingParams["fieldOrders"] = $fieldOrders;
        return $this;
    }

    public function select(array $query, array $options=null): ?array{
        return $this->selectConstraint($query, null, null, -1, -1, $options);
    }
    public function selectByPrimary(array $params, array $options=null): ?array{
        return $this->selectConstraint($params, "default", null, -1, -1, $options);
    }
    public function selectByUnique(array $params, string $uniqueName, array $options=null): ?array{
        return $this->selectConstraint($params, $uniqueName, null, -1, -1, $options);
    }
    public function selectByIndex(array $params, string $indexName, array $options=null): ?array{
        return $this->selectConstraint($params, $indexName, null, -1, -1, $options);
    }
    public function selectByWhere(string $sqlWhere, array $sqlParams, array $options=null): ?array{
        return $this->selectConstraint($sqlParams, null, $sqlWhere, -1, -1, $options);
    }
    public function selectMany(array $query, int $seekBegin=0, int $fetchNums=10, array $options=null): array{
        return $this->selectConstraint($query, null, null, $seekBegin, $fetchNums, $options);
    }
    public function selectManyByPrimary(array $params, int $seekBegin=0, int $fetchNums=10, array $options=null): ?array{
        return $this->selectConstraint($params, "default", null, $seekBegin, $fetchNums, $options);
    }
    public function selectManyByUnique(array $params, string $uniqueName, int $seekBegin=0, int $fetchNums=10, array $options=null): ?array{
        return $this->selectConstraint($params, $uniqueName, null, $seekBegin, $fetchNums, $options);
    }
    public function selectManyByIndex(array $params, string $indexName, int $seekBegin=0, int $fetchNums=10, array $options=null): ?array{
        return $this->selectConstraint($params, $indexName, null, $seekBegin, $fetchNums, $options);
    }
    public function selectManyByWhere(string $sqlWhere, array $sqlParams, int $seekBegin=0, int $fetchNums=10, array $options=null): array{
        return $this->selectConstraint($sqlParams, null, $sqlWhere, $seekBegin, $fetchNums, $options);
    }
    public function selectAll(array $query, array $options=null): array{
        return $this->selectConstraint($query, null, null, 0, 0, $options);
    }
    public function selectAllByPrimary(array $params, array $options=null): ?array{
        return $this->selectConstraint($params, "default", null, 0, 0, $options);
    }
    public function selectAllByUnique(array $params, string $uniqueName, array $options=null): ?array{
        return $this->selectConstraint($params, $uniqueName, null, 0, 0, $options);
    }
    public function selectAllByIndex(array $params, string $indexName, array $options=null): ?array{
        return $this->selectConstraint($params, $indexName, null, 0, 0, $options);
    }
    public function selectAllByWhere(string $sqlWhere, array $sqlParams, array $options=null): array{
        return $this->selectConstraint($sqlParams, null, $sqlWhere, 0, 0, $options);
    }

    public function insert(array $data, array $options=null): bool{
        $queryParams = $this->makeQueryParams($data);
        if(count($queryParams) == 0){
            throw new \Exception("no insert items for insert", 660307);
        }
        $fields = $names = [];
        foreach ($queryParams as $fieldName => $queryParam){
            $fields[] = $fieldName;
            $names[] = $queryParam["name"];
        }
        $sql = "INSERT INTO ". $this->tableName. " (". implode(",", $fields). ") VALUES (". implode(",", $names). ")";
        return $this->tfdo->execute($sql, $queryParams);
    }

    public function update(array $data, array $query, array $options=null): bool{
        return $this->doUpdateConstraint($data, $query, $options);
    }
    public function updateByPrimary(array $data, array $params, array $options=null): bool{
        return $this->updateConstraint($data, $params, "default", null, $options);
    }
    public function updateByUnique(array $data, array $params, string $uniqueName, array $options=null): bool{
        return $this->updateConstraint($data, $params, $uniqueName, null, $options);
    }
    public function updateByIndex(array $data, array $params, string $indexName, array $options=null): bool{
        return $this->updateConstraint($data, $params, $indexName, null, $options);
    }
    public function updateByWhere(string $sqlWhere, array $sqlParams, array $data, array $options=null): bool{
        return $this->updateConstraint($data, $sqlParams, null, $sqlWhere, $options);
    }

    public function delete(array $query): bool{
        $queryParams = $this->makeQueryParams($query);
        if(count($queryParams) == 0){
            throw new \Exception("no condition items for delete", 660315);
        }
        $params = [];
        $sql = "DELETE FROM ". $this->tableName. " WHERE ";
        foreach ($queryParams as $fieldName => $queryParam){
            $sql .= $fieldName. " = ". $queryParam["name"]. " AND ";
            $params[] = $queryParam;
        }
        $sql = substr($sql, 0, -5);
        return $this->tfdo->execute($sql, $params);
    }
    public function deleteByPrimary(array $params): bool{
        return $this->deleteConstraint($params, "default");
    }
    public function deleteByUnique(array $params, string $uniqueName): bool{
        return $this->deleteConstraint($params, $uniqueName);
    }
    public function deleteByIndex(array $params, string $indexName): bool{
        return $this->deleteConstraint($params, $indexName);
    }
    public function deleteByWhere(string $sqlWhere, array $sqlParams): bool{
        return $this->deleteConstraint($sqlParams, null, $sqlWhere);
    }

    public function getAutoIncrementField(): ?string{
        return $this->autoIncrementField;
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        if($this->autoIncrementField){
            return $this->tfdo->getLastInsertAutoIncrementValue();
        }
        return null;
    }
    public function getLastInsertData(): ?array{
        if($this->autoIncrementField){
            $row = $this->select([$this->autoIncrementField=>$this->getLastInsertAutoIncrementValue()]);
            if($row){
                return $row;
            }
        }
        return null;
    }

    public function getTFDO(): ?tfdo{
        return $this->tfdo;
    }
}