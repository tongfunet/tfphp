<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;

/**
 * Class tfdaoOneToOne
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoOneToOne extends tfdao {
    private array $methodChainingParams;
    private ?string $lastError;
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $tfphp, array $tables, array $relationParams, array $options=null){
        parent::__construct($tfphp);
        $this->methodChainingParams = [];
        $this->lastError = null;
        $this->tables = $tables;
        $this->relationParams = $relationParams;
        $this->options = $options;
        if(count($this->tables) < 2){
            throw new \Exception("the number of tables must be at least 2", 660401);
        }
        if((count($this->tables) - count($this->relationParams)) != 1){
            throw new \Exception("the number of relation parameters must be one more than the number of tables", 660402);
        }
        foreach ($this->relationParams as $k => $relationParam){
            if(!isset($this->relationParams[$k]["type"])) $this->relationParams[$k]["type"] = tfdao::RELATION_PARAM_TYPE_ARRAY;
            switch ($this->relationParams[$k]["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    if(!isset($relationParam["mapping"]) || !is_array($relationParam["mapping"])){
                        throw new \Exception("the relation parameter ". $k. " is of type array and require 'mapping' param", 660403);
                    }
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    if(empty($relationParam["sql"])){
                        throw new \Exception("the relation parameter ". $k. " is of type array and require 'sql' param", 660404);
                    }
                    preg_match_all("/a\.([a-zA-Z0-9\_\-]+)[\s\t\r\n]*\=[\s\t\r\n]*b\.([a-zA-Z0-9\_\-]+)/", $relationParam["sql"], $rgs);
                    $this->relationParams[$k]["mapping"] = [];
                    foreach ($rgs[1] as $m => $v){
                        $this->relationParams[$k]["mapping"][$v] = $rgs[2][$m];
                        $this->relationParams[$k]["sql"] = str_replace("a.". $v, "?", $this->relationParams[$k]["sql"]);
                    }
                    break;
                default:
                    throw new \Exception("invalid type of relation parameter", 660405);
            }
        }
    }
    private function cycleSelect(array $data, array $options=null): ?array{
        foreach ($this->tables as $k => $table){
            if($k == 0) continue;
            $curData = null;
            $relationParam = $this->relationParams[$k-1];
            switch ($relationParam["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    $query = [];
                    foreach ($relationParam["mapping"] as $fieldA => $fieldB) $query[$fieldB] = $data[$fieldA];
                    $curData = $table->select($query, $options);
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    $params = [];
                    foreach ($relationParam["mapping"] as $fieldA => $fieldB) $params[] = $data[$fieldA];
                    $curData = $table->selectByWhere($relationParam["sql"], $params, $options);
                    break;
            }
            if($curData === null){
                return null;
            }
            $data = ($data === null) ? $curData : array_merge($data, $curData);
        }
        return $data;
    }
    private function cycleInsert(tfdo $ds, array $data): bool{
        foreach ($this->tables as $k => $table){
            try{
                if(!$table->insert($data)){
                    $ds->rollback();
                    return false;
                }
                if($k == 0){
                    if($table->getAutoIncrementField()) $data[$table->getAutoIncrementField()] = $table->getLastInsertAutoIncrementValue();
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no insert items for insert)/", $e->getMessage())){
                    throw $e;
                }
            }
        }
        return true;
    }
    private function cycleUpdate(tfdo $ds, array $curData, array $data): bool{
        foreach ($this->tables as $k => $table){
            try{
                if($k == 0) continue;
                $relationParam = $this->relationParams[$k-1];
                switch ($relationParam["type"]){
                    case tfdao::RELATION_PARAM_TYPE_ARRAY:
                        $query = [];
                        foreach ($relationParam["mapping"] as $fieldA => $fieldB) $query[$fieldB] = $curData[$fieldA];
                        if(!$table->update($data, $query)){
                            $ds->rollback();
                            return false;
                        }
                        break;
                    case tfdao::RELATION_PARAM_TYPE_SQL:
                        $params = [];
                        foreach ($relationParam["mapping"] as $fieldA => $fieldB) $params[] = $curData[$fieldA];
                        if(!$table->updateByWhere($relationParam["sql"], $params, $data)){
                            $ds->rollback();
                            return false;
                        }
                        break;
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no update items for update|no condition items for update)/", $e->getMessage())){
                    $ds->rollback();
                    throw $e;
                }
            }
        }
        return true;
    }
    private function cycleDelete(tfdo $ds, array $curData): bool{
        foreach ($this->tables as $k => $table){
            try{
                if($k == 0) continue;
                $relationParam = $this->relationParams[$k-1];
                switch ($relationParam["type"]){
                    case tfdao::RELATION_PARAM_TYPE_ARRAY:
                        $query = [];
                        foreach ($relationParam["mapping"] as $fieldA => $fieldB) $query[$fieldB] = $curData[$fieldA];
                        if(!$table->delete($query)){
                            $ds->rollback();
                            return false;
                        }
                        break;
                    case tfdao::RELATION_PARAM_TYPE_SQL:
                        $params = [];
                        foreach ($relationParam["mapping"] as $fieldA => $fieldB) $params[] = $curData[$fieldA];
                        if(!$table->deleteByWhere($relationParam["sql"], $params)){
                            $ds->rollback();
                            return false;
                        }
                        break;
                    default:
                        throw new \Exception("invalid type of relation parameter", 660406);
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no condition items for delete)/", $e->getMessage())){
                    $ds->rollback();
                    throw $e;
                }
                return false;
            }
        }
        return true;
    }
    private function selectConstraint(array $params, string $constraintName=null, string $sqlWhere=null, array $options=null): ?array{
        if($options === null) $options = [];
        $options = array_merge($options, $this->methodChainingParams);
        $this->methodChainingParams = [];
        if($constraintName == null){
            $curData = $this->getTable(0)->select($params, $options);
        }
        else if($constraintName == "default"){
            $curData = $this->getTable(0)->selectByPrimary($params, $options);
        }
        else{
            $curData = $this->getTable(0)->selectByIndex($params, $constraintName, $options);
        }
        if($curData === null){
            return null;
        }
        $data = $this->cycleSelect($curData, $options);
        return $data;
    }
    public function updateConstraint(array $data, array $params, string $constraintName=null, array $options=null): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        if($constraintName == null){
            $curData = $this->select($params);
        }
        else if($constraintName == "default"){
            $curData = $this->selectByPrimary($params);
        }
        else{
            $curData = $this->selectByIndex($params, $constraintName);
        }
        if($curData === null){
            return false;
        }
        try{
            if($constraintName == null){
                $ret = $this->getTable(0)->update($data, $params);
            }
            else if($constraintName == "default"){
                $ret = $this->getTable(0)->updateByPrimary($data, $params);
            }
            else{
                $ret = $this->getTable(0)->updateByUnique($data, $params, $constraintName);
            }
            if(!$ret){
                return false;
            }
        }
        catch(\Exception $e){
            if(!preg_match("/(no update items for update|no condition items for update)/", $e->getMessage())){
                $ds->rollback();
                throw $e;
            }
        }
        if(!$this->cycleUpdate($ds, $curData, $data)){
            return false;
        }
        $ds->commit();
        return true;
    }
    public function deleteConstraint(array $params, string $constraintName=null): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        if($constraintName == null){
            $curData = $this->select($params);
        }
        else if($constraintName == "default"){
            $curData = $this->selectByPrimary($params);
        }
        else{
            $curData = $this->selectByIndex($params, $constraintName);
        }
        if($curData === null){
            return false;
        }
        if($constraintName == null){
            $ret = $this->getTable(0)->delete($params);
        }
        else if($constraintName == null){
            $ret = $this->getTable(0)->deleteByPrimary($params);
        }
        else{
            $ret = $this->getTable(0)->deleteByUnique($params, $constraintName);
        }
        if(!$ret){
            return false;
        }
        if(!$this->cycleDelete($ds, $curData)){
            return false;
        }
        $ds->commit();
        return true;
    }

    public function setFields(array $selectFields): tfdaoOneToOne{
        $this->methodChainingParams["selectFields"] = $selectFields;
        return $this;
    }
    public function setOrders(array $fieldOrders): tfdaoOneToOne{
        $this->methodChainingParams["fieldOrders"] = $fieldOrders;
        return $this;
    }

    public function select(array $query, array $options=null): ?array{
        return $this->selectConstraint($query, null, null, $options);
    }
    public function selectByPrimary(array $params, array $options=null): ?array{
        return $this->selectConstraint($params, "default", null, $options);
    }
    public function selectByUnique(array $params, string $uniqueName, array $options=null): ?array{
        return $this->selectConstraint($params, $uniqueName, null, $options);
    }
    public function selectByIndex(array $params, string $indexName, array $options=null): ?array{
        return $this->selectConstraint($params, $indexName, null, $options);
    }
    public function selectByWhere(string $sqlWhere, array $sqlParams, array $options=null): ?array{
        return $this->selectConstraint($sqlParams, null, $sqlWhere, $options);
    }

    public function insert(array $data, array $options=null): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        if(!$this->cycleInsert($ds, $data)){
            return false;
        }
        $ds->commit();
        return true;
    }

    public function update(array $data, array $query, array $options=null): bool{
        return $this->updateConstraint($data, $query, null, $options);
    }
    public function updateByPrimary(array $data, array $params, array $options=null): bool{
        return $this->updateConstraint($data, $params, "default", $options);
    }
    public function updateByUnique(array $data, array $params, string $uniqueName, array $options=null): bool{
        return $this->updateConstraint($data, $params, $uniqueName, $options);
    }
    public function updateByIndex(array $data, array $params, string $indexName, array $options=null): bool{
        return $this->updateConstraint($data, $params, $indexName, $options);
    }

    public function delete(array $query): bool{
        return $this->deleteConstraint($query);
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

    public function getTable(int $index): ?tfdaoSingle{
        return $this->tables[$index];
    }

    public function getAutoIncrementField(): ?string{
        return $this->tables[0]->getAutoIncrementField();
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        return $this->tables[0]->getLastInsertAutoIncrementValue();
    }
    public function getLastInsertData(): ?array{
        return $this->tables[0]->getLastInsertData();
    }

    public function getTFDO(): ?tfdo{
        return $this->tables[0]->getTFDO();
    }

    public function getLastError(): ?string{
        return $this->lastError;
    }
}