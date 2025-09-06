<?php 

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

/**
 * Class tfdaoManyToMany
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoManyToMany extends tfdao{
    private array $A;
    private ?string $D;
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $AF, array $A3, array $A7, array $A9=null){
        parent::__construct($AF);
        $this->A = [];
        $this->D = null;
        $this->tables = $A3;
        $this->relationParams = $A7;
        $this->options = $A9;
        if(count($this->tables) != 3){
            throw new \Exception("the number of tables must be 3");
        }
        if(count($this->relationParams) != 2){
            throw new \Exception("the number of relation parameters must be 2");
        }
        foreach ($this->relationParams as $BA => $BF){
            if(!isset($this->relationParams[$BA]["type"])) $this->relationParams[$BA]["type"] = tfdao::RELATION_PARAM_TYPE_ARRAY;
            switch ($this->relationParams[$BA]["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    if(!isset($BF["mapping"]) || !is_array($BF["mapping"])){
                        throw new \Exception("the relation parameter ". $BA. " is of type array and require 'mapping' param");
                    }
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    if(empty($BF["sql"])){
                        throw new \Exception("the relation parameter ". $BA. " is of type array and require 'sql' param");
                    }
                    preg_match_all("/a\.([a-zA-Z0-9\_\-]+)[\s\t\r\n]*\=[\s\t\r\n]*m\.([a-zA-Z0-9\_\-]+)/", $BF["sql"], $C5);
                    $this->relationParams[$BA]["mapping"] = [];
                    foreach ($C5[1] as $C8 => $C9){
                        $this->relationParams[$BA]["mapping"][$C9] = $C5[2][$C8];
                        $this->relationParams[$BA]["sql"] = str_replace("a.". $C9, "?", $this->relationParams[$BA]["sql"]);
                    }
                    break;
                default:
                    throw new \Exception("invalid type of relation parameter");
            }
        }
    }

    public function setAFields(array $CD): tfdaoManyToMany{
        $this->A["selectAFields"] = $CD;
        return $this;
    }
    public function setBFields(array $CD): tfdaoManyToMany{
        $this->A["selectBFields"] = $CD;
        return $this;
    }

    public function bind(array $CE, array $D4, array $A9=null): bool{
        $D7 = $this->getTable(1);
        $DB = $this->relationParams[0];
        $DD = $this->relationParams[1];
        foreach ($CE as $E0){
            $E1 = [];
            foreach ($DB["mapping"] as $E2 => $E7) $E1[$E7] = $E0[$E2];
            foreach ($D4 as $EC){
                $F1 = $E1;
                foreach ($DD["mapping"] as $E7 => $F5) $F1[$E7] = $EC[$F5];
                if(!$D7->select($F1)){
                    if(!$D7->insert($F1)){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    public function unbind(array $CE, array $D4, array $A9=null): bool{
        $D7 = $this->getTable(1);
        $DB = $this->relationParams[0];
        $DD = $this->relationParams[1];
        foreach ($CE as $E0){
            $E1 = [];
            foreach ($DB["mapping"] as $E2 => $E7) $E1[$E7] = $E0[$E2];
            foreach ($D4 as $EC){
                $F1 = $E1;
                foreach ($DD["mapping"] as $E7 => $F5) $F1[$E7] = $EC[$F5];
                if(!$D7->delete($F1)){
                    return false;
                }
            }
        }
        return true;
    }
    public function replace(array $CE, array $D4, array $A9=null): bool{
        $D7 = $this->getTable(1);
        $DB = $this->relationParams[0];
        $DD = $this->relationParams[1];
        $F9 = [];
        $FC = [];
        foreach ($CE as $E0){
            $E1 = [];
            foreach ($DB["mapping"] as $E2 => $E7) $E1[$E7] = $E0[$E2];
            foreach ($D4 as $EC){
                $F1 = $E1;
                foreach ($DD["mapping"] as $E7 => $F5) $F1[$E7] = $EC[$F5];
                if(!$D7->select($F1)){
                    if(!$D7->insert($F1)){
                        return false;
                    }
                }
            }
        }
        foreach ($CE as $E0){
            $FE = [];
            foreach ($DB["mapping"] as $E2 => $E7) $FE[$E7] = $E0[$E2];
            $F9[] = $FE;
        }
        foreach ($D4 as $EC){
            $A05 = [];
            foreach ($DD["mapping"] as $E7 => $F5) $A05[$E7] = $EC[$F5];
            $FC[] = $A05;
        }
        foreach ($FC as $A05){
            $A06 = $D7->selectAll($A05);
            if($A06 === null){
                return false;
            }
            foreach ($A06 as $A0C){
                $A0E = false;
                foreach ($F9 as $FE) if(empty(array_diff_assoc($FE, $A0C))) $A0E = true;
                if(!$A0E){
                    if(!$D7->delete($A0C)){
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function getADataAll(array $EC, array $A9=null): array{
        if($A9 === null) $A9 = [];
        if(empty($A9["maxDataSelect"])) $A9["maxDataSelect"] = 1000;
        $A14 = $this->getTable(0);
        $D7 = $this->getTable(1);
        $DB = $this->relationParams[0];
        $DD = $this->relationParams[1];
        $A05 = [];
        foreach ($DD["mapping"] as $E7 => $F5) $A05[$E7] = $EC[$F5];
        $A06 = $D7->selectMany($A05, 0, $A9["maxDataSelect"]);
        $A17 = [];
        if(is_array($A06)){
            foreach ($A06 as $A0C){
                $FE = [];
                foreach ($DB["mapping"] as $E2 => $E7) $FE[$E7] = $A0C[$E2];
                if(!empty($this->A["selectAFields"])) $A14 = $A14->setFields($this->A["selectAFields"]);
                $E0 = $A14->select($FE);
                if($E0){
                    $A17[] = $E0;
                }
            }
        }
        return $A17;
    }
    public function getBDataAll(array $E0, array $A9=null): array{
        if($A9 === null) $A9 = [];
        if(empty($A9["maxDataSelect"])) $A9["maxDataSelect"] = 1000;
        $D7 = $this->getTable(1);
        $A1A = $this->getTable(2);
        $DB = $this->relationParams[0];
        $DD = $this->relationParams[1];
        $FE = [];
        foreach ($DB["mapping"] as $E2 => $E7) $FE[$E7] = $E0[$E2];
        $A06 = $D7->selectMany($FE, 0, $A9["maxDataSelect"]);
        $A1C = [];
        if(is_array($A06)){
            foreach ($A06 as $A0C){
                $A05 = [];
                foreach ($DD["mapping"] as $E7 => $F5) $A05[$E7] = $A0C[$F5];
                if(!empty($this->A["selectBFields"])) $A1A = $A1A->setFields($this->A["selectBFields"]);
                $EC = $A1A->select($A05);
                if($EC){
                    $A1C[] = $EC;
                }
            }
        }
        return $A1C;
    }

    public function getTable(int $A1D): ?tfdaoSingle{
        return $this->tables[$A1D];
    }

    public function getLastError(): ?string{
        return $this->D;
    }
}