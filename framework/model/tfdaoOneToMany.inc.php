<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoOneToMany extends tfdao{
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $A3, array $A, array $C, array $E=null){
        parent::__construct($A3);
        $this->tables = $A;
        $this->relationParams = $C;
        $this->options = $E;
        if(count($this->tables) != 2){
            throw new \Exception("the number of tables should be at east 2");
        }
        if(count($this->tables) != (count($this->relationParams["fieldMapping"]) + 1)){
            throw new \Exception("the number of relation param 'fieldMapping' must be ". strval(count($this->tables) - 1));
        }
    }
    public function getTable(int $A5): ?tfdaoSingle{
        return $this->tables[$A5];
    }
}