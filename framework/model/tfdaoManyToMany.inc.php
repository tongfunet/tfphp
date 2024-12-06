<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoManyToMany extends tfdao{
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $A3, array $A, array $B, array $A0=null){
        parent::__construct($A3);
        $this->tables = $A;
        $this->relationParams = $B;
        $this->options = $A0;
        if(count($this->tables) != 3){
            throw new \Exception("the number of tables must be 3");
        }
        if(count($this->relationParams["fieldMapping"]) != 2){
            throw new \Exception("the number of relation param 'fieldMapping' must be 2");
        }
    }
    private function doSelect(array $A8): ?array{
        $AC = [];
        foreach ($this->relationParams["fieldMapping"][0] as $B1 => $field2) $AC[$field2] = $A8[$B1];
        $B3 = $this->tables[2]->selectAll($AC);
        if($B3){
            $B9 = [];
            foreach ($B3 as $CDatum){
                $BC = [];
                foreach ($this->relationParams["fieldMapping"][1] as $B1 => $field2) $BC[$field2] = $CDatum[$B1];
                $C0 = $this->tables[1]->select($BC);
                if($C0){
                    $B9[] = $C0;
                }
            }
            return $B9;
        }
        return null;
    }
    private function doSelectReverse(array $A8): ?array{
        $BC = [];
        foreach ($this->relationParams["fieldMapping"][1] as $B1 => $field2) $BC[$field2] = $A8[$B1];
        $B3 = $this->tables[2]->selectAll($BC);
        if($B3){
            $B9 = [];
            foreach ($B3 as $CDatum){
                $AC = [];
                foreach ($this->relationParams["fieldMapping"][0] as $B1 => $field2) $AC[$field2] = $CDatum[$B1];
                $C0 = $this->tables[0]->select($AC);
                if($C0){
                    $B9[] = $C0;
                }
            }
            return $B9;
        }
        return null;
    }
    public function select(array $C1): ?array{
        $A8 = $this->tables[0]->select($C1);
        $B9 = $this->doSelect($A8) ;
        return $B9;
    }
    public function constraintSelect(array $C5, string $CB="default"): ?array{
        $A8 = $this->tables[0]->constraintSelect($C5, $CB);
        $B9 = $this->doSelect($A8) ;
        return $B9;
    }
    public function keySelect(array $C5, string $CB="default"): ?array{
        return $this->constraintSelect($C5, $CB);
    }
    public function insert(array $CD, array $C0, array $A8=null): bool{
        $CE = $this->tfphp->getDataSource();
        $CE->beginTransaction();
        try{
            if($A8 === null){
                $A8 = [];
            }
            foreach ($this->relationParams["fieldMapping"][0] as $B1 => $field2) $A8[$field2] = $CD[$B1];
            foreach ($this->relationParams["fieldMapping"][1] as $B1 => $field2) $A8[$field2] = $C0[$B1];
            if(!$this->tables[2]->insert($A8, ["checkConstraints"=>true])){
                $CE->rollback();
                return false;
            }
        }
        catch(\Exception $e){
            throw $e;
        }
        $CE->commit();
        return true;
    }
    public function insertMultiple(array $CD, array $C0, array $A8=null): bool{
        foreach ($C0 as $BDatum){
            if(!$this->insert($CD, $BDatum, $A8)){
                return false;
            }
        }
        return true;
    }
    public function delete(array $CD, array $C0): bool{
        $CE = $this->tfphp->getDataSource();
        $CE->beginTransaction();
        try{
            $C1 = [];
            foreach ($this->relationParams["fieldMapping"][0] as $B1 => $field2) $C1[$field2] = $CD[$B1];
            foreach ($this->relationParams["fieldMapping"][1] as $B1 => $field2) $C1[$field2] = $C0[$B1];
            if(!$this->tables[2]->delete($C1)){
                $CE->rollback();
                return false;
            }
        }
        catch(\Exception $e){
            throw $e;
        }
        $CE->commit();
        return true;
    }
    public function deleteMultiple(array $CD, array $C0): bool{
        foreach ($C0 as $BDatum){
            if(!$this->delete($CD, $BDatum)){
                return false;
            }
        }
        return true;
    }
    public function getTable(int $D4): ?tfdaoSingle{
        return $this->tables[$D4];
    }
}