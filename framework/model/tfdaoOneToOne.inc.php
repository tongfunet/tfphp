<?php 

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;

/**
 * Class tfdaoOneToOne
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoOneToOne extends tfdao{
    private array $A;
    private ?string $D;
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $AA, array $A1, array $A5, array $A8=null){
        parent::__construct($AA);
        $this->A = [];
        $this->D = null;
        $this->tables = $A1;
        $this->relationParams = $A5;
        $this->options = $A8;
        if(count($this->tables) < 2){
            throw new \Exception("the number of tables must be at least 2");
        }
        if((count($this->tables) - count($this->relationParams)) != 1){
            throw new \Exception("the number of relation parameters must be one more than the number of tables");
        }
        foreach ($this->relationParams as $AE => $B2){
            if(!isset($this->relationParams[$AE]["type"])) $this->relationParams[$AE]["type"] = tfdao::RELATION_PARAM_TYPE_ARRAY;
            switch ($this->relationParams[$AE]["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    if(!isset($B2["mapping"]) || !is_array($B2["mapping"])){
                        throw new \Exception("the relation parameter ". $AE. " is of type array and require 'mapping' param");
                    }
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    if(empty($B2["sql"])){
                        throw new \Exception("the relation parameter ". $AE. " is of type array and require 'sql' param");
                    }
                    preg_match_all("/a\.([a-zA-Z0-9\_\-]+)[\s\t\r\n]*\=[\s\t\r\n]*b\.([a-zA-Z0-9\_\-]+)/", $B2["sql"], $B6);
                    $this->relationParams[$AE]["mapping"] = [];
                    foreach ($B6[1] as $BA => $BF){
                        $this->relationParams[$AE]["mapping"][$BF] = $B6[2][$BA];
                        $this->relationParams[$AE]["sql"] = str_replace("a.". $BF, "?", $this->relationParams[$AE]["sql"]);
                    }
                    break;
                default:
                    throw new \Exception("invalid type of relation parameter");
            }
        }
    }
    private function C1(array $C4, array $A8=null): ?array{
        foreach ($this->tables as $AE => $C6){
            if($AE == 0) continue;
            $CA = null;
            $B2 = $this->relationParams[$AE-1];
            switch ($B2["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    $CB = [];
                    foreach ($B2["mapping"] as $CD => $D0) $CB[$D0] = $C4[$CD];
                    $CA = $C6->select($CB, $A8);
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    $D1 = [];
                    foreach ($B2["mapping"] as $CD => $D0) $D1[] = $C4[$CD];
                    $CA = $C6->sqlWhereSelect($B2["sql"], $D1, $A8);
                    break;
            }
            if($CA === null){
                return null;
            }
            $C4 = ($C4 === null) ? $CA : array_merge($C4, $CA);
        }
        return $C4;
    }
    private function D5(tfdo $D9, array $C4): bool{
        foreach ($this->tables as $AE => $C6){
            try{
                if(!$C6->insert($C4, ["checkConstraints"=>($AE > 0)])){
                    $D9->rollback();
                    return false;
                }
                if($AE == 0){
                    if($C6->getAutoIncrementField()) $C4[$C6->getAutoIncrementField()] = $C6->getLastInsertAutoIncrementValue();
                }
            }
            catch(\Exception $DE){
                if(!preg_match("/(no insert items for insert)/", $DE->getMessage())){
                    throw $DE;
                }
            }
        }
        return true;
    }
    private function E3(tfdo $D9, array $CA, array $C4): bool{
        foreach ($this->tables as $AE => $C6){
            try{
                if($AE == 0) continue;
                $B2 = $this->relationParams[$AE-1];
                switch ($B2["type"]){
                    case tfdao::RELATION_PARAM_TYPE_ARRAY:
                        $CB = [];
                        foreach ($B2["mapping"] as $CD => $D0) $CB[$D0] = $CA[$CD];
                        if(!$C6->update($CB, $C4, [
                            "skipEmptyUpdateItems"=>true
                        ])){
                            $D9->rollback();
                            return false;
                        }
                        break;
                    case tfdao::RELATION_PARAM_TYPE_SQL:
                        $D1 = [];
                        foreach ($B2["mapping"] as $CD => $D0) $D1[] = $CA[$CD];
                        if(!$C6->sqlWhereUpdateAll($B2["sql"], $D1, $C4, [
                            "skipEmptyUpdateItems"=>true
                        ])){
                            $D9->rollback();
                            return false;
                        }
                        break;
                }
            }
            catch(\Exception $DE){
                if(!preg_match("/(no update items for update|no condition items for update)/", $DE->getMessage())){
                    $D9->rollback();
                    throw $DE;
                }
            }
        }
        return true;
    }
    private function E9(tfdo $D9, array $CA): bool{
        foreach ($this->tables as $AE => $C6){
            try{
                if($AE == 0) continue;
                $B2 = $this->relationParams[$AE-1];
                switch ($B2["type"]){
                    case tfdao::RELATION_PARAM_TYPE_ARRAY:
                        $CB = [];
                        foreach ($B2["mapping"] as $CD => $D0) $CB[$D0] = $CA[$CD];
                        if(!$C6->delete($CB)){
                            $D9->rollback();
                            return false;
                        }
                        break;
                    case tfdao::RELATION_PARAM_TYPE_SQL:
                        $D1 = [];
                        foreach ($B2["mapping"] as $CD => $D0) $D1[] = $CA[$CD];
                        if(!$C6->sqlWhereDelete($B2["sql"], $D1)){
                            $D9->rollback();
                            return false;
                        }
                        break;
                    default:
                        throw new \Exception("invalid type of relation parameter");
                }
            }
            catch(\Exception $DE){
                if(!preg_match("/(no condition items for delete)/", $DE->getMessage())){
                    $D9->rollback();
                    throw $DE;
                }
                return false;
            }
        }
        return true;
    }

    public function setFields(array $ED): tfdaoOneToOne{
        $this->A["selectFields"] = $ED;
        return $this;
    }
    public function setOrders(array $F0): tfdaoOneToOne{
        $this->A["fieldOrders"] = $F0;
        return $this;
    }

    public function select(array $CB, array $A8=null): ?array{
        if($A8 === null) $A8 = [];
        $A8 = array_merge($A8, $this->A);
        $this->A = [];
        $CA = $this->getTable(0)->select($CB, $A8);
        if($CA === null){
            return null;
        }
        $C4 = $this->C1($CA, $A8);
        return $C4;
    }
    public function constraintSelect(array $D1, string $F5="default", array $A8=null): ?array{
        if($A8 === null) $A8 = [];
        $A8 = array_merge($A8, $this->A);
        $this->A = [];
        $CA = $this->getTable(0)->constraintSelect($D1, $F5, $A8);
        if($CA === null){
            return null;
        }
        $C4 = $this->C1($CA, $A8);
        return $C4;
    }
    public function keySelect(array $D1, string $F5="default", array $A8=null): ?array{
        return $this->constraintSelect($D1, $F5, $A8);
    }
    public function sqlWhereSelect(string $FB, array $D1, array $A8=null): ?array{
        if($A8 === null) $A8 = [];
        $A8 = array_merge($A8, $this->A);
        $this->A = [];
        $CA = $this->getTable(0)->sqlWhereSelect($FB, $D1, $A8);
        if($CA === null){
            return null;
        }
        $C4 = $this->C1($CA, $A8);
        return $C4;
    }

    public function insert(array $C4, array $A8=null): bool{
        $D9 = $this->tfphp->getDataSource();
        $D9->beginTransaction();
        if(!$this->D5($D9, $C4)){
            return false;
        }
        $D9->commit();
        return true;
    }

    public function update(array $CB, array $C4, array $A8=null): bool{
        $D9 = $this->tfphp->getDataSource();
        $D9->beginTransaction();
        $CA = $this->select($CB);
        if($CA === null){
            return false;
        }
        if(!$this->getTable(0)->update($CB, $C4, [
            "skipEmptyUpdateItems"=>true
        ])){
            return false;
        }
        if(!$this->E3($D9, $CA, $C4)){
            return false;
        }
        $D9->commit();
        return true;
    }
    public function constraintUpdate(array $D1, array $C4, string $F5="default", array $A8=null): bool{
        $D9 = $this->tfphp->getDataSource();
        $D9->beginTransaction();
        $CA = $this->constraintSelect($D1, $F5);
        if($CA === null){
            return false;
        }
        if(!$this->getTable(0)->constraintUpdate($D1, $C4)){
            return false;
        }
        if(!$this->E3($D9, $CA, $C4)){
            return false;
        }
        $D9->commit();
        return true;
    }
    public function keyUpdate(array $D1, array $C4, string $F5="default", array $A8=null): bool{
        return $this->constraintUpdate($D1, $C4, $F5);
    }

    public function delete(array $CB): bool{
        $D9 = $this->tfphp->getDataSource();
        $D9->beginTransaction();
        $CA = $this->select($CB);
        if($CA === null){
            return false;
        }
        if(!$this->getTable(0)->delete($CB)){
            return false;
        }
        if(!$this->E9($D9, $CA)){
            return false;
        }
        $D9->commit();
        return true;
    }
    public function constraintDelete(array $D1, string $F5="default"): bool{
        $D9 = $this->tfphp->getDataSource();
        $D9->beginTransaction();
        $CA = $this->constraintSelect($D1, $F5);
        if($CA === null){
            return false;
        }
        if(!$this->getTable(0)->constraintDelete($D1)){
            return false;
        }
        if(!$this->E9($D9, $CA)){
            return false;
        }
        $D9->commit();
        return true;
    }
    public function keyDelete(array $D1, string $F5="default"): bool{
        return $this->constraintDelete($D1, $F5);
    }

    public function getTable(int $FF): ?tfdaoSingle{
        return $this->tables[$FF];
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
        return $this->D;
    }
}