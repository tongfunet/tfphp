<?php 

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

/**
 * Class tfdaoOneToMany
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoOneToMany extends tfdao{
    private ?string $A;
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $A5, array $C, array $E, array $A1=null){
        parent::__construct($A5);
        $this->A = null;
        $this->tables = $C;
        $this->relationParams = $E;
        $this->options = $A1;
        if(count($this->tables) != 2){
            throw new \Exception("the number of tables must be 2");
        }
        if(count($this->relationParams) != 1){
            throw new \Exception("the number of relation parameters must be 1");
        }
        foreach ($this->relationParams as $A8 => $A9){
            if(!isset($this->relationParams[$A8]["type"])) $this->relationParams[$A8]["type"] = tfdao::RELATION_PARAM_TYPE_ARRAY;
            switch ($this->relationParams[$A8]["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    if(!isset($A9["mapping"]) || !is_array($A9["mapping"])){
                        throw new \Exception("the relation parameter ". $A8. " is of type array and require 'mapping' param");
                    }
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    if(empty($A9["sql"])){
                        throw new \Exception("the relation parameter ". $A8. " is of type array and require 'sql' param");
                    }
                    preg_match_all("/a\.([a-zA-Z0-9\_\-]+)[\s\t\r\n]*\=[\s\t\r\n]*b\.([a-zA-Z0-9\_\-]+)/", $A9["sql"], $AC);
                    $this->relationParams[$A8]["mapping"] = [];
                    foreach ($AC[1] as $AF => $B3){
                        $this->relationParams[$A8]["mapping"][$B3] = $AC[2][$AF];
                        $this->relationParams[$A8]["sql"] = str_replace("a.". $B3, "?", $this->relationParams[$A8]["sql"]);
                    }
                    break;
                default:
                    throw new \Exception("invalid type of relation parameter");
            }
        }
    }
}