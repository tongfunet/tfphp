<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\model;

use tfphp\framework\database\tfdo;
use tfphp\framework\tfphp;
use tfphp\controller\api\tfdaoOneToOne\fullUser;

class tfmodel{
    protected tfphp $tfphp;
    private string $dataSourceName;
    private array $dao;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->dataSourceName = "default";
        $this->dao = [];
    }
    public function setDataSourceName(string $name){
        $this->dataSourceName = $name;
    }
    public function setDSN(string $name){
        $this->dataSourceName = $name;
    }
    public function setDAOOneToOne(string $key, array $singleTableNames, array $relationParams){
        $singleDAO = [];
        foreach ($singleTableNames as $singleTableName) $singleDAO[] = $this->getSG($singleTableName);
        $this->dao[$key] = new tfdaoOneToOne($this->tfphp, $singleDAO, $relationParams);
    }
    public function setDAOOneToMany(string $key, array $singleTableNames, array $relationParams){
        $singleDAO = [];
        foreach ($singleTableNames as $singleTableName) $singleDAO[] = $this->getSG($singleTableName);
        $this->dao[$key] = new tfdaoOneToMany($this->tfphp, $singleDAO, $relationParams);
    }
    public function setDAOManyToMany(string $key, array $singleTableNames, array $relationParams){
        $singleDAO = [];
        foreach ($singleTableNames as $singleTableName) $singleDAO[] = $this->getSG($singleTableName);
        $this->dao[$key] = new tfdaoManyToMany($this->tfphp, $singleDAO, $relationParams);
    }
    public function setO2O(string $key, array $singleTableNames, array $relationParams){
        $this->setDAOOneToOne($key, $singleTableNames, $relationParams);
    }
    public function setO2M(string $key, array $singleTableNames, array $relationParams){
        $this->setDAOOneToMany($key, $singleTableNames, $relationParams);
    }
    public function setM2M(string $key, array $singleTableNames, array $relationParams){
        $this->setDAOManyToMany($key, $singleTableNames, $relationParams);
    }
    public function getDataSource(string $dataSourceName=null): tfdo{
        if($dataSourceName === null) $dataSourceName = $this->dataSourceName;
        return $this->tfphp->getDataSource($dataSourceName);
    }
    public function getDS(string $dataSourceName=null): tfdo{
        return $this->getDataSource($dataSourceName);
    }
    public function getDAOSingle(string $key): tfdaoSingle{
        if(!isset($this->dao[$key])){
            $daoClassName = "tfphp\\model\\dao\\". (($this->dataSourceName == "default") ? "__". $this->dataSourceName. "__" : $this->dataSourceName). "\\". $key;
            if(!class_exists($daoClassName)){
                $daoBuilder = new tfdaoBuilder($this->tfphp);
                $result = $daoBuilder->build($this->dataSourceName);
                if($result === null){
                    throw new \Exception("data source '". $this->dataSourceName. "' is invalid", 660201);
                }
            }
            if(!class_exists($daoClassName)){
                throw new \Exception("table '". $key. "' of data source '". $this->dataSourceName. "' is invalid", 660202);
            }
            $daoClass = new \ReflectionClass($daoClassName);
            $this->dao[$key] = $daoClass->newInstanceArgs([$this->tfphp]);
        }
        if(!isset($this->dao[$key])){
            throw new \Exception("single dao '". $key. "' is not set", 660203);
        }
        return $this->dao[$key];
    }
    public function getSG(string $key): tfdaoSingle{
        return $this->getDAOSingle($key);
    }
    public function getDAOOneToOne(string $key): tfdaoOneToOne{
        return $this->dao[$key];
    }
    public function getDAOOneToMany(string $key): tfdaoOneToMany{
        return $this->dao[$key];
    }
    public function getDAOManyToMany(string $key): tfdaoManyToMany{
        return $this->dao[$key];
    }
    public function getO2O(string $key): tfdaoOneToOne{
        return $this->getDAOOneToOne($key);
    }
    public function getO2M(string $key): tfdaoOneToMany{
        return $this->getDAOOneToMany($key);
    }
    public function getM2M(string $key): tfdaoManyToMany{
        return $this->getDAOManyToMany($key);
    }
}