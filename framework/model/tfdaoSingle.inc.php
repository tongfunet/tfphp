<?php 

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;

/**
 * Class tfdaoSingle
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoSingle extends tfdao{
    private array $A;
    protected ?tfdo $tfdo;
    protected string $tableName;
    protected array $tableFields;
    protected array $tableConstraints;
    protected ?string $autoIncrementField;
    public function __construct(tfphp $B8, array $BD){
        parent::__construct($B8);
        if(empty($BD["name"])){
            throw new \Exception("param 'name' is missing");
        }
        if(empty($BD["fields"])){
            throw new \Exception("param 'fields' is missing");
        }
        else if(!is_array($BD["fields"]) || count($BD["fields"]) == 0){
            throw new \Exception("param 'fields' is invalid");
        }
        $this->A = [];
        $this->tfdo = $this->tfphp->getDataSource($BD["dataSource"]);
        $this->tableName = $BD["name"];
        $this->tableFields = $BD["fields"];
        $this->tableConstraints = $BD["constraints"];
        $this->autoIncrementField = (isset($BD["autoIncrementField"])) ? $BD["autoIncrementField"] : null;
    }
    private function C4(array $C8, array $CC=null): array{
        if($CC === null) $CC = [];
        $D0 = [];
        $D3 = (isset($CC["startFieldNameNumber"])) ? intval($CC["startFieldNameNumber"]) : 0;
        foreach ($this->tableFields as $D4 => $D8){
            if(isset($C8[$D4])){
                $D0[$D4] = [
                    "name"=>":f". strval($D3),
                    "type"=>$D8["type"],
                    "value"=>$C8[$D4]
                ];
                $D3 ++;
            }
        }
        return $D0;
    }
    private function DE(array $D0, string $E4, array $CC=null): array{
        if($CC === null) $CC = [];
        $E8 = [];
        $D3 = 0;
        $E9 = $this->tableConstraints[$E4];
        foreach ($E9 as $D4){
            if(isset($this->tableFields[$D4])){
                $E8[$D4] = [
                    "name"=>":f". strval($D3),
                    "type"=>$this->tableFields[$D4]["type"],
                    "value"=>$D0[$D3]
                ];
                $D3 ++;
            }
        }
        return $E8;
    }
    private function EE(array $C8, array $CC=null): array{
        if($CC === null) $CC = [];
        $F1 = $this->C4($C8);
        if(!isset($CC["selectAll"]) || !is_bool($CC["selectAll"])) $CC["selectAll"] = false;
        if(!$CC["selectAll"] && count($F1) == 0){
            throw new \Exception("no condition items for select");
        }
        if(!isset($CC["selectFields"]) || !is_array($CC["selectFields"])) $CC["selectFields"] = [];
        $F7 = "SELECT ". $this->A04($CC["selectFields"]). " FROM ". $this->tableName. " WHERE ";
        $D0 = [];
        foreach ($F1 as $D4 => $FB){
            $F7 .= $D4. " = ". $FB["name"]. " AND ";
            $D0[] = $FB;
        }
        $F7 = substr($F7, 0, -5);
        if(isset($CC["fieldOrders"]) && is_array($CC["fieldOrders"])) $F7 .= $this->A0F($CC["fieldOrders"]);
        return [$F7, $D0];
    }
    private function FD(array $D0, string $E4, array $CC=null): array{
        if($CC === null) $CC = [];
        if(!isset($this->tableConstraints[$E4])){
            throw new \Exception("constraint '". $E4. "' of table '". $this->tableName. "' for select is invalid");
        }
        $F1 = $this->DE($D0, $E4);
        if(!isset($CC["selectAll"]) || !is_bool($CC["selectAll"])) $CC["selectAll"] = false;
        if(!$CC["selectAll"] && count($F1) == 0){
            throw new \Exception("no condition items for select");
        }
        if(!isset($CC["selectFields"]) || !is_array($CC["selectFields"])) $CC["selectFields"] = [];
        $F7 = "SELECT ". $this->A04($CC["selectFields"]). " FROM ". $this->tableName. " WHERE ";
        $E8 = [];
        foreach ($F1 as $D4 => $FB){
            $F7 .= $D4. " = ". $FB["name"]. " AND ";
            $E8[] = $FB;
        }
        $F7 = substr($F7, 0, -5);
        return [$F7, $E8];
    }
    private function A04(array $A06): string{
        if(count($A06) > 0){
            foreach ($A06 as $A0B => $A0C){
                if(!isset($this->tableFields[$A0C])) unset($A06[$A0B]);
            }
        }
        return (count($A06) > 0) ? implode(", ", $A06) : "*";
    }
    private function A0F(array $A14): string{
        $A15 = [];
        if(count($A14) > 0){
            foreach ($A14 as $A17 => $A18){
                if(isset($this->tableFields[$A17])) $A15[] = $A17. " ". $A18;
            }
        }
        return (count($A15) > 0) ? " ORDER BY ". implode(", ", $A15) : "";
    }

    public function setFields(array $A06): tfdaoSingle{
        $this->A["selectFields"] = $A06;
        return $this;
    }
    public function setOrders(array $A14): tfdaoSingle{
        $this->A["fieldOrders"] = $A14;
        return $this;
    }

    public function select(array $C8, array $CC=null): ?array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        list($F7, $D0) = $this->EE($C8, $CC);
        return $this->tfdo->fetchOne($F7, $D0);
    }
    public function constraintSelect(array $D0, string $E4="default", array $CC=null): ?array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        list($F7, $E8) = $this->FD($D0, $E4, $CC);
        return $this->tfdo->fetchOne($F7, $E8);
    }
    public function keySelect(array $D0, string $E4="default", array $CC=null): ?array{
        return $this->constraintSelect($D0, $E4, $CC);
    }
    public function sqlWhereSelect(string $A1C, array $D0, array $CC=null): ?array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        if(!isset($CC["selectFields"]) || !is_array($CC["selectFields"])) $CC["selectFields"] = [];
        $F7 = "SELECT ". $this->A04($CC["selectFields"]). " FROM ". $this->tableName. " WHERE ". $A1C;
        $F7 = preg_replace("/\@(int|str)/", "", $F7);
        if(isset($CC["fieldOrders"]) && is_array($CC["fieldOrders"])) $F7 .= $this->A0F($CC["fieldOrders"]);
        return $this->tfdo->fetchOne2($F7, $D0);
    }

    public function selectMany(array $C8, int $A22=0, int $A28=10, array $CC=null): array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        list($F7, $D0) = $this->EE($C8, $CC);
        return $this->tfdo->fetchMany($F7, $D0, $A22, $A28);
    }
    public function constraintSelectMany(array $D0, string $E4="default", int $A22=0, int $A28=10, array $CC=null): array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        list($F7, $E8) = $this->FD($D0, $E4, $CC);
        return $this->tfdo->fetchMany($F7, $E8, $A22, $A28);
    }
    public function keySelectMany(array $D0, string $E4="default", int $A22=0, int $A28=10, array $CC=null): array{
        return $this->constraintSelectMany($D0, $E4, $A22, $A28, $CC);
    }
    public function sqlWhereSelectMany(string $A1C, array $D0, int $A22=0, int $A28=10, array $CC=null): array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        if(!isset($CC["selectFields"]) || !is_array($CC["selectFields"])) $CC["selectFields"] = [];
        $F7 = "SELECT ". $this->A04($CC["selectFields"]). " FROM ". $this->tableName. " WHERE ". $A1C;
        $F7 = preg_replace("/\@(int|str)/", "", $F7);
        if(isset($CC["fieldOrders"]) && is_array($CC["fieldOrders"])) $F7 .= $this->A0F($CC["fieldOrders"]);
        return $this->tfdo->fetchMany2($F7, $D0, $A22, $A28);
    }

    public function selectAll(array $C8, array $CC=null): array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        $CC["selectAll"] = true;
        list($F7, $D0) = $this->EE($C8, $CC);
        return $this->tfdo->fetchAll($F7, $D0);
    }
    public function constraintSelectAll(array $D0, string $E4="default", array $CC=null): array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        $CC["selectAll"] = true;
        list($F7, $E8) = $this->FD($D0, $E4, $CC);
        return $this->tfdo->fetchAll($F7, $E8);
    }
    public function keySelectAll(array $D0, string $E4="default", array $CC=null): array{
        return $this->constraintSelectAll($D0, $E4, $CC);
    }
    public function sqlWhereSelectAll(string $A1C, array $D0, array $CC=null): array{
        if($CC === null) $CC = [];
        $CC = array_merge($CC, $this->A);
        $this->A = [];
        if(!isset($CC["selectFields"]) || !is_array($CC["selectFields"])) $CC["selectFields"] = [];
        $F7 = "SELECT ". $this->A04($CC["selectFields"]). " FROM ". $this->tableName. " WHERE ". $A1C;
        $F7 = preg_replace("/\@(int|str)/", "?", $F7);
        if(isset($CC["fieldOrders"]) && is_array($CC["fieldOrders"])) $F7 .= $this->A0F($CC["fieldOrders"]);
        return $this->tfdo->fetchAll2($F7, $D0);
    }

    public function insert(array $A2D, array $CC=null): bool{
        $F1 = $this->C4($A2D);
        if(count($F1) == 0){
            throw new \Exception("no insert items for insert");
        }
        if(isset($CC["checkConstraints"]) && $CC["checkConstraints"]){
            foreach ($this->tableConstraints as $A31 => $E9){
                $A34 = null;
                try{
                    $E8 = [];
                    foreach ($E9 as $D4) if(isset($A2D[$D4])) $E8[] = $A2D[$D4];
                    $A34 = $this->constraintSelect($E8, $A31);
                }
                catch(\Exception $A35){ }
                if($A34){
                    throw new \Exception("data of constraint '". $A31. "' of table '". $this->tableName. "' for insert is duplicate");
                }
            }
        }
        $A38 = $A3A = [];
        foreach ($F1 as $D4 => $FB){
            $A38[] = $D4;
            $A3A[] = $FB["name"];
        }
        $F7 = "INSERT INTO ". $this->tableName. " (". implode(",", $A38). ") VALUES (". implode(",", $A3A). ")";
        return $this->tfdo->execute($F7, $F1);
    }

    public function update(array $C8, array $A2D, array $CC=null): bool{
        $F1 = $this->C4($C8);
        $A40 = $this->C4($A2D, ["startFieldNameNumber"=>count($F1)]);
        if(count($F1) == 0){
            throw new \Exception("no condition items for update");
        }
        if(count($A40) == 0){
            if(!isset($CC["skipEmptyUpdateItems"]) || !$CC["skipEmptyUpdateItems"]){
                throw new \Exception("no update items for update");
            }
            else{
                return true;
            }
        }
        if(isset($CC["checkConstraints"]) && $CC["checkConstraints"]){
            $A45 = $this->select($C8);
            if($A45 === null){
                throw new \Exception("data of table '". $this->tableName. "' for update is not found");
            }
            $A4B = serialize($A45);
            foreach ($this->tableConstraints as $A31 => $E9){
                $A34 = null;
                try{
                    $E8 = [];
                    foreach ($E9 as $D4) if(isset($A2D[$D4])) $E8[] = $A2D[$D4];
                    $A34 = $this->constraintSelect($E8, $A31);
                }
                catch(\Exception $A35){ }
                if($A34 && serialize($A34) != $A4B){
                    throw new \Exception("data of constraint '". $A31. "' of table '". $this->tableName. "' for update is duplicate");
                }
            }
        }
        $D0 = [];
        $F7 = "UPDATE ". $this->tableName. " SET ";
        foreach ($A40 as $D4 => $A4F){
            $F7 .= $D4. " = ". $A4F["name"]. ", ";
            $D0[] = $A4F;
        }
        $F7 = substr($F7, 0, -2);
        $F7 .= " WHERE ";
        foreach ($F1 as $D4 => $FB){
            $F7 .= $D4. " = ". $FB["name"]. " AND ";
            $D0[] = $FB;
        }
        $F7 = substr($F7, 0, -5);
        return $this->tfdo->execute($F7, $D0);
    }
    public function constraintUpdate(array $D0, array $A2D, string $E4="default", array $CC=null): bool{
        $F1 = $this->DE($D0, $E4);
        $C8 = [];
        foreach ($F1 as $A52 => $FB) $C8[$A52] = $FB["value"];
        return $this->update($C8, $A2D, $CC);
    }
    public function keyUpdate(array $D0, array $A2D, string $E4="default", array $CC=null): bool{
        return $this->constraintUpdate($D0, $A2D, $E4, $CC);
    }
    public function sqlWhereUpdateAll(string $A1C, array $A58, array $A2D, array $CC=null): bool{
        $A40 = $this->C4($A2D);
        if(count($A40) == 0){
            if(!isset($CC["skipEmptyUpdateItems"]) || !$CC["skipEmptyUpdateItems"]){
                throw new \Exception("no update items for update");
            }
            else{
                return true;
            }
        }
        if(isset($CC["checkConstraints"]) && $CC["checkConstraints"]){
            $A5E = $this->sqlWhereSelectAll($A1C, $A58);
            foreach ($A5E as $A45){
                $A4B = serialize($A45);
                foreach ($this->tableConstraints as $A31 => $E9){
                    $A34 = null;
                    try{
                        $E8 = [];
                        foreach ($E9 as $D4) if(isset($A2D[$D4])) $E8[] = $A2D[$D4];
                        $A34 = $this->constraintSelect($E8, $A31);
                    }
                    catch(\Exception $A35){ }
                    if($A34 && serialize($A34) != $A4B){
                        throw new \Exception("data of constraint '". $A31. "' of table '". $this->tableName. "' for update is duplicate");
                    }
                }
            }
        }
        $D0 = [];
        $F7 = "UPDATE ". $this->tableName. " SET ";
        foreach ($A40 as $D4 => $A4F){
            $F7 .= (is_numeric($A4F["value"])) ? $D4. " = @int, " : $D4. " = @str, ";
            $D0[] = $A4F["value"];
        }
        $F7 = substr($F7, 0, -2);
        $F7 .= " WHERE ";
        $F7 .= $A1C;
        foreach ($A58 as $A61) $D0[] = $A61;
        return $this->tfdo->execute($F7, $D0);
    }

    public function delete(array $C8): bool{
        $F1 = $this->C4($C8);
        if(count($F1) == 0){
            throw new \Exception("no condition items for delete");
        }
        $D0 = [];
        $F7 = "DELETE FROM ". $this->tableName. " WHERE ";
        foreach ($F1 as $D4 => $FB){
            $F7 .= $D4. " = ". $FB["name"]. " AND ";
            $D0[] = $FB;
        }
        $F7 = substr($F7, 0, -5);
        return $this->tfdo->execute($F7, $D0);
    }
    public function constraintDelete(array $D0, string $E4="default"): bool{
        $F1 = $this->DE($D0, $E4);
        $C8 = [];
        foreach ($F1 as $A52 => $FB) $C8[$A52] = $FB["value"];
        return $this->delete($C8);
    }
    public function keyDelete(array $D0, string $E4="default"): bool{
        return $this->constraintDelete($D0, $E4);
    }
    public function sqlWhereDeleteAll(string $A1C, array $A58): bool{
        $D0 = [];
        $F7 = "DELETE FROM ". $this->tableName. " WHERE ";
        $F7 .= $A1C;
        foreach ($A58 as $A61) $D0[] = $A61;
        return $this->tfdo->execute($F7, $D0);
    }

    public function getAutoIncrementField(): ?string{
        return $this->autoIncrementField;
    }
    public function getLastInsertAutoIncrementValue(): ?int{
        if($this->autoIncrementField){
            return $this->tfdo->getLastInsertAutoIncrementValue();
        }
        return null;
    }
    public function getLastInsertData(): ?array{
        if($this->autoIncrementField){
            $A67 = $this->select([$this->autoIncrementField=>$this->getLastInsertAutoIncrementValue()]);
            if($A67){
                return $A67;
            }
        }
        return null;
    }

    public function getTFDO(): ?tfdo{
        return $this->tfdo;
    }
}