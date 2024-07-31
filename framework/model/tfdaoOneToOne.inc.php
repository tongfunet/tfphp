<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoOneToOne extends tfdao {
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $tfphp, array $tables, array $relationParams, array $options=null){
        parent::__construct($tfphp);
        $this->tables = $tables;
        $this->relationParams = $relationParams;
        $this->options = $options;
        if(count($this->tables) < 2){
            throw new \Exception("the number of tables must be at least 2");
        }
        if((count($this->tables) - count($this->relationParams["fieldMapping"])) != 1){
            throw new \Exception("the number of relation param 'fieldMapping' must be one more than the number of tables");
        }
    }
    public function select(array $query, string $constraintName=null): ?array{
        $data = [];
        foreach ($this->tables as $k => $table){
            if($k > 0){
                $query = [];
                foreach ($this->relationParams["fieldMapping"][$k-1] as $field1 => $field2){
                    $query[$field2] = $data[$field1];
                }
                $constraintName = null;
            }
            $curData = $table->select($query, $constraintName);
            if($curData === null){
                return null;
            }
            $data = array_merge($data, $curData);
        }
        return $data;
    }
    public function insert(array $data): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        foreach ($this->tables as $k => $table){
            try{
                if(!$table->insert($data, [
                    "checkConstraints"=>true
                ])){
                    $ds->rollback();
                    return false;
                }
                if($k == 0){
                    if($table->getAutoIncrementField()){
                        $data[$table->getAutoIncrementField()] = $table->getLastInsertAutoIncrementValue();
                    }
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
    public function update(array $query, array $data, string $constraintName=null): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        $curData = $this->select($query, $constraintName);
        if($curData === null){
            return false;
        }
        foreach ($this->tables as $k => $table){
            if($k > 0){
                $query = [];
                foreach ($this->relationParams["fieldMapping"][$k-1] as $field1 => $field2){
                    $query[$field2] = $curData[$field1];
                }
                $constraintName = null;
            }
            try{
                if(!$table->update($query, $data, $constraintName, [
                    "checkConstraints"=>true
                ])){
                    $ds->rollback();
                    return false;
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no update items for update|no condition items for update)/", $e->getMessage())){
                    throw $e;
                }
            }
        }
        $ds->commit();
        return true;
    }
    public function delete(array $query, string $constraintName=null): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        $curData = $this->select($query, $constraintName);
        if($curData === null){
            return false;
        }
        foreach ($this->tables as $k => $table){
            if($k > 0){
                $query = [];
                foreach ($this->relationParams["fieldMapping"][$k-1] as $field1 => $field2){
                    $query[$field2] = $curData[$field1];
                }
                $constraintName = null;
            }
            try{
                if(!$table->delete($query, $constraintName)){
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
    public function getAutoIncrementField(): ?string{
        return $this->tables[0]->getAutoIncrementField();
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        return $this->tables[0]->getLastInsertAutoIncrementValue();
    }
    public function getLastInsert(): ?array{
        return $this->tables[0]->getLastInsert();
    }
}