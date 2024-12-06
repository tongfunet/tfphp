<?php

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;
use tfphp\controller\api\tfdaoOneToOne\fullUser;

class tfmodel{
    protected tfphp $tfphp;
    private string $dataSourceName;
    private array $dao;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $this->dataSourceName = "default";
        $this->dao = [];
    }
    public function setDataSourceName(string $A8){
        $this->dataSourceName = $A8;
    }
    public function setDAOOneToOne(string $AE, array $AF, array $B0){
        $B3 = [];
        foreach ($AF as $singleTableName) $B3[] = $this->getSG($singleTableName);
        $this->dao[$AE] = new tfdaoOneToOne($this->tfphp, $B3, [
            "fieldMapping"=>$B0
        ]);
    }
    public function setDAOOneToMany(string $AE, array $AF, array $B0){
        $B3 = [];
        foreach ($AF as $singleTableName) $B3[] = $this->getSG($singleTableName);
        $this->dao[$AE] = new tfdaoOneToMany($this->tfphp, $B3, [
            "fieldMapping"=>$B0
        ]);
    }
    public function setDAOManyToMany(string $AE, array $AF, array $B0){
        $B3 = [];
        foreach ($AF as $singleTableName) $B3[] = $this->getSG($singleTableName);
        $this->dao[$AE] = new tfdaoManyToMany($this->tfphp, $B3, [
            "fieldMapping"=>$B0
        ]);
    }
    public function getDataSource(string $A1=null): tfdo{
        if($A1 === null) $A1 = $this->dataSourceName;
        return $this->tfphp->getDataSource($A1);
    }
    public function getDS(string $A1=null): tfdo{
        return $this->getDataSource($A1);
    }
    public function getDAOSingle(string $AE): tfdaoSingle{
        if(!isset($this->dao[$AE])){
            $B5 = "tfphp\\model\\dao\\". (($this->dataSourceName == "default") ? "__". $this->dataSourceName. "__" : $this->dataSourceName). "\\". $AE;
            if(!class_exists($B5)){
                $BA = new tfdaoBuilder($this->tfphp);
                $BD = $BA->build($this->dataSourceName) ;
                if($BD === null){
                    throw new \Exception("data source '". $this->dataSourceName. "' is invalid");
                }
            }
            if(!class_exists($B5)){
                throw new \Exception("table '". $AE. "' of data source '". $this->dataSourceName. "' is invalid");
            }
            $C1 = new \ReflectionClass($B5) ;
            $this->dao[$AE] = $C1->newInstanceArgs([$this->tfphp]);
        }
        if(!isset($this->dao[$AE])){
            throw new \Exception("single dao '". $AE. "' is not set");
        }
        return $this->dao[$AE];
    }
    public function getSG(string $AE): tfdaoSingle{
        return $this->getDAOSingle($AE);
    }
    public function getDAOOneToOne(string $AE): tfdaoOneToOne{
        return $this->dao[$AE];
    }
    public function getDAOOneToMany(string $AE): tfdaoOneToMany{
        return $this->dao[$AE];
    }
    public function getDAOManyToMany(string $AE): tfdaoManyToMany{
        return $this->dao[$AE];
    }
    public function getO2O(string $AE): tfdaoOneToOne{
        return $this->getDAOOneToOne($AE);
    }
    public function getO2M(string $AE): tfdaoOneToMany{
        return $this->getDAOOneToMany($AE);
    }
    public function getM2M(string $AE): tfdaoManyToMany{
        return $this->getDAOManyToMany($AE);
    }
}