<?php

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;

class tfdaoOneToOne extends tfdao{
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $A6, array $A, array $E, array $A3=null){
        parent::__construct($A6);
        $this->tables = $A;
        $this->relationParams = $E;
        $this->options = $A3;
        if(count($this->tables) < 2){
            throw new \Exception("the number of tables must be at least 2");
        }
        if((count($this->tables) - count($this->relationParams["fieldMapping"])) != 1){
            throw new \Exception("the number of relation param 'fieldMapping' must be one more than the number of tables");
        }
    }
    private function cycleSelect(?array $AC): ?array{
        if($AC === null){
            return null;
        }
        foreach ($this->tables as $AD => $table){
            if($AD == 0) continue;
            $B3 = [];
            foreach ($this->relationParams["fieldMapping"][$AD-1] as $B5 => $field2) $B3[$field2] = $AC[$B5];
            $B9 = $table->select($B3);
            if($B9 === null){
                return null;
            }
            $AC = array_merge($AC, $B9) ;
        }
        return $AC;
    }
    private function cycleUpdate(tfdo $BE, ?array $B9, array $AC): bool{
        if($B9 === null){
            return false;
        }
        foreach ($this->tables as $AD => $table){
            if($AD > 0){
                $B3 = [];
                foreach ($this->relationParams["fieldMapping"][$AD-1] as $B5 => $field2) $B3[$field2] = $B9[$B5];
            }
            else{
                $B3 = [];
                foreach ($this->relationParams["fieldMapping"][0] as $B5 => $field2) $B3[$B5] = $B9[$B5];
            }
            try{
                if(!$table->update($B3, $AC, ["checkConstraints"=>true])){
                    $BE->rollback();
                    return false;
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no update items for update|no condition items for update)/", $e->getMessage())){
                    $BE->rollback();
                    throw $e;
                }
            }
        }
        return true;
    }
    private function cycleDelete(tfdo $BE, ?array $B9): bool{
        if($B9 === null){
            return false;
        }
        foreach ($this->tables as $AD => $table){
            if($AD > 0){
                $B3 = [];
                foreach ($this->relationParams["fieldMapping"][$AD-1] as $B5 => $field2) $B3[$field2] = $B9[$B5];
            }
            else{
                $B3 = [];
                foreach ($this->relationParams["fieldMapping"][0] as $B5 => $field2) $B3[$B5] = $B9[$B5];
            }
            try{
                if(!$table->delete($B3)){
                    $BE->rollback();
                    return false;
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no condition items for delete)/", $e->getMessage())){
                    $BE->rollback();
                    throw $e;
                }
                return false;
            }
        }
        return true;
    }
    public function select(array $B3): ?array{
        $AC = $this->tables[0]->select($B3);
        $AC = $this->cycleSelect($AC) ;
        return $AC;
    }
    public function constraintSelect(array $C3, string $C5="default"): ?array{
        $AC = $this->tables[0]->constraintSelect($C3, $C5);
        $AC = $this->cycleSelect($AC) ;
        return $AC;
    }
    public function keySelect(array $C3, string $C5="default"): ?array{
        return $this->constraintSelect($C3, $C5);
    }
    public function insert(array $AC): bool{
        $BE = $this->tfphp->getDataSource();
        $BE->beginTransaction();
        foreach ($this->tables as $AD => $table){
            try{
                if(!$table->insert($AC, ["checkConstraints"=>true])){
                    $BE->rollback();
                    return false;
                }
                if($AD == 0){
                    if($table->getAutoIncrementField()) $AC[$table->getAutoIncrementField()] = $table->getLastInsertAutoIncrementValue();
                }
            }
            catch(\Exception $e){
                if(!preg_match("/(no insert items for insert)/", $e->getMessage())){
                    throw $e;
                }
            }
        }
        $BE->commit();
        return true;
    }
    public function update(array $B3, array $AC): bool{
        $BE = $this->tfphp->getDataSource();
        $BE->beginTransaction();
        $B9 = $this->select($B3) ;
        if(!$this->cycleUpdate($BE, $B9, $AC)){
            return false;
        }
        $BE->commit();
        return true;
    }
    public function constraintUpdate(array $C3, array $AC, string $C5="default"): bool{
        $BE = $this->tfphp->getDataSource();
        $BE->beginTransaction();
        $B9 = $this->constraintSelect($C3, $C5) ;
        if(!$this->cycleUpdate($BE, $B9, $AC)){
            return false;
        }
        $BE->commit();
        return true;
    }
    public function keyUpdate(array $C3, array $AC, string $C5="default"): bool{
        return $this->constraintUpdate($C3, $AC, $C5);
    }
    public function delete(array $B3): bool{
        $BE = $this->tfphp->getDataSource();
        $BE->beginTransaction();
        $B9 = $this->select($B3) ;
        if(!$this->cycleDelete($BE, $B9)){
            return false;
        }
        $BE->commit();
        return true;
    }
    public function constraintDelete(array $C3, string $C5="default"): bool{
        $BE = $this->tfphp->getDataSource();
        $BE->beginTransaction();
        $B9 = $this->constraintSelect($C3, $C5) ;
        if(!$this->cycleDelete($BE, $B9)){
            return false;
        }
        $BE->commit();
        return true;
    }
    public function keyDelete(array $C3, string $C5="default"): bool{
        return $this->constraintDelete($C3, $C5);
    }
    public function getTable(int $CA): ?tfdaoSingle{
        return $this->tables[$CA];
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