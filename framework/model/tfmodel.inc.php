<?php 

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;
use tfphp\controller\api\tfdaoOneToOne\fullUser;

class tfmodel{
    protected tfphp $tfphp;
    private string $E;
    private array $F;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $this->E = "default";
        $this->F = [];
    }
    public function setDataSourceName(string $A5){
        $this->E = $A5;
    }
    public function setDSN(string $A5){
        $this->E = $A5;
    }
    public function setDAOOneToOne(string $A6, array $AB, array $AD){
        $AE = [];
        foreach ($AB as $B2) $AE[] = $this->getSG($B2);
        $this->F[$A6] = new tfdaoOneToOne($this->tfphp, $AE, $AD);
    }
    public function setDAOOneToMany(string $A6, array $AB, array $AD){
        $AE = [];
        foreach ($AB as $B2) $AE[] = $this->getSG($B2);
        $this->F[$A6] = new tfdaoOneToMany($this->tfphp, $AE, $AD);
    }
    public function setDAOManyToMany(string $A6, array $AB, array $AD){
        $AE = [];
        foreach ($AB as $B2) $AE[] = $this->getSG($B2);
        $this->F[$A6] = new tfdaoManyToMany($this->tfphp, $AE, $AD);
    }
    public function getDataSource(string $B6=null): tfdo{
        if($B6 === null) $B6 = $this->E;
        return $this->tfphp->getDataSource($B6);
    }
    public function getDS(string $B6=null): tfdo{
        return $this->getDataSource($B6);
    }
    public function getDAOSingle(string $A6): tfdaoSingle{
        if(!isset($this->F[$A6])){
            $BB = "tfphp\\model\\dao\\". (($this->E == "default") ? "__". $this->E. "__" : $this->E). "\\". $A6;
            if(!class_exists($BB)){
                $C0 = new tfdaoBuilder($this->tfphp);
                $C6 = $C0->build($this->E);
                if($C6 === null){
                    throw new \Exception("data source '". $this->E. "' is invalid");
                }
            }
            if(!class_exists($BB)){
                throw new \Exception("table '". $A6. "' of data source '". $this->E. "' is invalid");
            }
            $CC = new \ReflectionClass($BB);
            $this->F[$A6] = $CC->newInstanceArgs([$this->tfphp]);
        }
        if(!isset($this->F[$A6])){
            throw new \Exception("single dao '". $A6. "' is not set");
        }
        return $this->F[$A6];
    }
    public function getSG(string $A6): tfdaoSingle{
        return $this->getDAOSingle($A6);
    }
    public function getDAOOneToOne(string $A6): tfdaoOneToOne{
        return $this->F[$A6];
    }
    public function getDAOOneToMany(string $A6): tfdaoOneToMany{
        return $this->F[$A6];
    }
    public function getDAOManyToMany(string $A6): tfdaoManyToMany{
        return $this->F[$A6];
    }
    public function getO2O(string $A6): tfdaoOneToOne{
        return $this->getDAOOneToOne($A6);
    }
    public function getO2M(string $A6): tfdaoOneToMany{
        return $this->getDAOOneToMany($A6);
    }
    public function getM2M(string $A6): tfdaoManyToMany{
        return $this->getDAOManyToMany($A6);
    }
}