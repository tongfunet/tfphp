<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoOneToMany extends tfdao {
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $tfphp, array $tables, array $relationParams, array $options=null){
        parent::__construct($tfphp);
        $this->tables = $tables;
        $this->relationParams = $relationParams;
        $this->options = $options;
        if(count($this->tables) < 2){
            throw new \Exception("the number of tables should be at east 2");
        }
        if(count($this->tables) != (count($this->relationParams["fieldMapping"]) + 1)){
            throw new \Exception("the number of relation param 'fieldMapping' must be ". strval(count($this->tables) - 1));
        }
    }
    public function update(array $data, string $constraintName=null): bool{
        $ds = $this->tfphp->getDataSource();
        $ds->beginTransaction();
        $query = $data[0];
        $updateData = [];
        foreach ($this->tables as $k => $table){
            if($k > 0){
                foreach ($this->relationParams["fieldMapping"][$k-1] as $field1 => $field2){
                    $updateData[$field2] = $data[$k][$field2];
                }
            }
        }
        try{
            if(!$this->tables[0]->update($query, $updateData, $constraintName, [
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
        $ds->commit();
        return true;
    }
    public function getTable(int $index): ?tfdaoSingle{
        return $this->tables[$index];
    }
}