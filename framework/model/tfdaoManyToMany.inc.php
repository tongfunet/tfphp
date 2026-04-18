<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

/**
 * Class tfdaoManyToMany
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoManyToMany extends tfdao {
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
        if(count($this->tables) != 3){
            throw new \Exception("the number of tables must be 3", 660601);
        }
        if(count($this->relationParams) != 2){
            throw new \Exception("the number of relation parameters must be 2", 660602);
        }
        foreach ($this->relationParams as $k => $relationParam){
            if(!isset($this->relationParams[$k]["type"])) $this->relationParams[$k]["type"] = tfdao::RELATION_PARAM_TYPE_ARRAY;
            switch ($this->relationParams[$k]["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    if(!isset($relationParam["mapping"]) || !is_array($relationParam["mapping"])){
                        throw new \Exception("the relation parameter ". $k. " is of type array and require 'mapping' param", 660603);
                    }
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    if(empty($relationParam["sql"])){
                        throw new \Exception("the relation parameter ". $k. " is of type array and require 'sql' param", 660604);
                    }
                    preg_match_all("/a\.([a-zA-Z0-9\_\-]+)[\s\t\r\n]*\=[\s\t\r\n]*m\.([a-zA-Z0-9\_\-]+)/", $relationParam["sql"], $rgs);
                    $this->relationParams[$k]["mapping"] = [];
                    foreach ($rgs[1] as $m => $v){
                        $this->relationParams[$k]["mapping"][$v] = $rgs[2][$m];
                        $this->relationParams[$k]["sql"] = str_replace("a.". $v, "?", $this->relationParams[$k]["sql"]);
                    }
                    break;
                default:
                    throw new \Exception("invalid type of relation parameter", 660605);
            }
        }
    }

    public function setAFields(array $fieldNames): tfdaoManyToMany{
        $this->methodChainingParams["selectAFields"] = $fieldNames;
        return $this;
    }
    public function setBFields(array $fieldNames): tfdaoManyToMany{
        $this->methodChainingParams["selectBFields"] = $fieldNames;
        return $this;
    }

    public function bind(array $ADataArr, array $BDataArr, array $options=null): bool{
        $MTable = $this->getTable(1);
        $AMRelationParam = $this->relationParams[0];
        $MBRelationParam = $this->relationParams[1];
        foreach ($ADataArr as $AData){
            $origMiddleQuery = [];
            foreach ($AMRelationParam["mapping"] as $fieldA => $fieldM) $origMiddleQuery[$fieldM] = $AData[$fieldA];
            foreach ($BDataArr as $BData){
                $middleQuery = $origMiddleQuery;
                foreach ($MBRelationParam["mapping"] as $fieldM => $fieldB) $middleQuery[$fieldM] = $BData[$fieldB];
                if(!$MTable->select($middleQuery)){
                    if(!$MTable->insert($middleQuery)){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    public function unbind(array $ADataArr, array $BDataArr, array $options=null): bool{
        $MTable = $this->getTable(1);
        $AMRelationParam = $this->relationParams[0];
        $MBRelationParam = $this->relationParams[1];
        foreach ($ADataArr as $AData){
            $origMiddleQuery = [];
            foreach ($AMRelationParam["mapping"] as $fieldA => $fieldM) $origMiddleQuery[$fieldM] = $AData[$fieldA];
            foreach ($BDataArr as $BData){
                $middleQuery = $origMiddleQuery;
                foreach ($MBRelationParam["mapping"] as $fieldM => $fieldB) $middleQuery[$fieldM] = $BData[$fieldB];
                if(!$MTable->delete($middleQuery)){
                    return false;
                }
            }
        }
        return true;
    }
    public function replace(array $ADataArr, array $BDataArr, array $options=null): bool{
        $MTable = $this->getTable(1);
        $AMRelationParam = $this->relationParams[0];
        $MBRelationParam = $this->relationParams[1];
        $AQueries = [];
        $MQueries = [];
        foreach ($ADataArr as $AData){
            $origQuery = [];
            foreach ($AMRelationParam["mapping"] as $fieldA => $fieldM) $origQuery[$fieldM] = $AData[$fieldA];
            $AQueries[] = $origQuery;
            foreach ($BDataArr as $BData){
                $myQuery = $origQuery;
                foreach ($MBRelationParam["mapping"] as $fieldM => $fieldB) $myQuery[$fieldM] = $BData[$fieldB];
                $MQueries[] = $myQuery;
                if(!$MTable->select($myQuery)){
                    if(!$MTable->insert($myQuery)){
                        return false;
                    }
                }
            }
        }
        foreach ($AQueries as $AQuery){
            $allMData = $MTable->selectAll($AQuery);
            if($allMData === null){
                return false;
            }
            foreach ($allMData as $MData){
                $found = false;
                foreach ($MQueries as $MQuery) if(empty(array_diff_assoc($MQuery, $MData))) $found = true;
                if(!$found){
                    if(!$MTable->delete($MData)){
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function getADataAll(array $BData, array $options=null): array{
        if($options === null) $options = [];
        if(empty($options["maxDataSelect"])) $options["maxDataSelect"] = 1000;
        $ATable = $this->getTable(0);
        $MTable = $this->getTable(1);
        $AMRelationParam = $this->relationParams[0];
        $MBRelationParam = $this->relationParams[1];
        $middleQuery = [];
        foreach ($MBRelationParam["mapping"] as $fieldM => $fieldB) $middleQuery[$fieldM] = $BData[$fieldB];
        $allMData = $MTable->selectMany($middleQuery, 0, $options["maxDataSelect"]);
        $allAData = [];
        if(is_array($allMData)){
            foreach ($allMData as $MData){
                $AQuery = [];
                foreach ($AMRelationParam["mapping"] as $fieldA => $fieldM) $AQuery[$fieldA] = $MData[$fieldM];
                if(!empty($this->methodChainingParams["selectAFields"])) $ATable = $ATable->setFields($this->methodChainingParams["selectAFields"]);
                $AData = $ATable->select($AQuery);
                if($AData){
                    $allAData[] = $AData;
                }
            }
        }
        return $allAData;
    }
    public function getBDataAll(array $AData, array $options=null): array{
        if($options === null) $options = [];
        if(empty($options["maxDataSelect"])) $options["maxDataSelect"] = 1000;
        $MTable = $this->getTable(1);
        $BTable = $this->getTable(2);
        $AMRelationParam = $this->relationParams[0];
        $MBRelationParam = $this->relationParams[1];
        $middleQuery = [];
        foreach ($AMRelationParam["mapping"] as $fieldA => $fieldM) $middleQuery[$fieldM] = $AData[$fieldA];
        $allMData = $MTable->selectMany($middleQuery, 0, $options["maxDataSelect"]);
        $allBData = [];
        if(is_array($allMData)){
            foreach ($allMData as $MData){
                $BQuery = [];
                foreach ($MBRelationParam["mapping"] as $fieldM => $fieldB) $BQuery[$fieldB] = $MData[$fieldM];
                if(!empty($this->methodChainingParams["selectBFields"])) $BTable = $BTable->setFields($this->methodChainingParams["selectBFields"]);
                $BData = $BTable->select($BQuery);
                if($BData){
                    $allBData[] = $BData;
                }
            }
        }
        return $allBData;
    }

    public function getTable(int $index): ?tfdaoSingle{
        return $this->tables[$index];
    }

    public function getLastError(): ?string{
        return $this->lastError;
    }
}