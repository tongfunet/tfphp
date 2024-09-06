<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoManyToMany extends tfdao {
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $tfphp, array $tables, array $relationParams, array $options=null){
        parent::__construct($tfphp);
        $this->tables = $tables;
        $this->relationParams = $relationParams;
        $this->options = $options;
        if(count($this->tables) != 3){
            throw new \Exception("the number of tables must be 3");
        }
        if(count($this->relationParams["fieldMapping"]) != 2){
            throw new \Exception("the number of relation param 'fieldMapping' must be 2");
        }
    }
    public function select(array $query, string $constraintName=null): ?array{
        $ATable = $this->tables[0];
        $AData = $ATable->select($query, $constraintName);
        if(!$AData){
            return null;
        }
        $CTable = $this->tables[2];
        $queryC = [];
        foreach ($this->relationParams["fieldMapping"][0] as $k => $v){
            $queryC[$v] = $AData[$k];
        }
        $CData = $CTable->selectAll($queryC);
        if(!$CData){
            return null;
        }
        $BTable = $this->tables[1];
        $resultData = [];
        foreach ($CData as $CDatum){
            $queryB = [];
            foreach ($this->relationParams["fieldMapping"][1] as $k => $v){
                $queryB[$v] = $CDatum[$k];
            }
            $BDatum = $BTable->select($queryB);
            if(!$BDatum){
                return null;
            }
            $resultData[] = array_merge($AData, $CDatum, $BDatum);
        }
        return $resultData;
    }
    public function insert(array $data): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        $insertData = [];
        foreach ($this->relationParams["fieldMapping"][0] as $field1 => $field2){
            $insertData[$field2] = $data[0][$field2];
        }
        foreach ($this->relationParams["fieldMapping"][1] as $field1 => $field2){
            $insertData[$field2] = $data[1][$field2];
        }
        try{
            if(!$this->tables[2]->insert($insertData)){
                $ds->rollback();
                return false;
            }
        }
        catch(\Exception $e){
            if(!preg_match("/(no insert items for insert)/", $e->getMessage())){
                throw $e;
            }
        }
        $ds->commit();
        return true;
    }
    public function insertMultiple(array $data): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        $insertData = [];
        foreach ($this->relationParams["fieldMapping"][0] as $field1 => $field2){
            $insertData[$field2] = $data[0][$field2];
        }
        foreach ($data[1] as $subData){
            try{
                foreach ($this->relationParams["fieldMapping"][1] as $field1 => $field2){
                    $insertData[$field2] = $subData[$field2];
                }
                if(!$this->tables[2]->insert($insertData)){
                    $ds->rollback();
                    return false;
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no insert items for insert)/", $e->getMessage())){
                    throw $e;
                }
            }
        }
        $ds->commit();
        return true;
    }
    public function delete(array $data): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        $query = [];
        foreach ($this->relationParams["fieldMapping"][0] as $field1 => $field2){
            $query[$field2] = $data[0][$field2];
        }
        foreach ($this->relationParams["fieldMapping"][1] as $field1 => $field2){
            $query[$field2] = $data[1][$field2];
        }
        try{
            if(!$this->tables[2]->delete($query)){
                $ds->rollback();
                return false;
            }
        }
        catch(\Exception $e){
            if(!preg_match("/(no insert items for insert)/", $e->getMessage())){
                throw $e;
            }
        }
        $ds->commit();
        return true;
    }
    public function deleteMultiple(array $data): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        $query = [];
        foreach ($this->relationParams["fieldMapping"][0] as $field1 => $field2){
            $query[$field2] = $data[0][$field2];
        }
        foreach ($data[1] as $subData){
            try{
                foreach ($this->relationParams["fieldMapping"][1] as $field1 => $field2){
                    $query[$field2] = $subData[$field2];
                }
                if(!$this->tables[2]->delete($query)){
                    $ds->rollback();
                    return false;
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no condition items for delete)/", $e->getMessage())){
                    throw $e;
                }
            }
        }
        $ds->commit();
        return true;
    }
    public function getTable(int $index): ?tfdaoSingle{
        return $this->tables[$index];
    }
}