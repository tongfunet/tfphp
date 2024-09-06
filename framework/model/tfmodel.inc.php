<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfmodel{
    protected tfphp $tfphp;
    private array $dao;
    public function __construct(tfphp $tfphp, array $dao){
        $this->tfphp = $tfphp;
        $this->dao = $dao;
    }
    public function setDAO(string $key, tfdao $dao){
        $this->dao[$key] = $dao;
    }
    public function getDAOSingle(string $key): tfdaoSingle{
        return $this->dao[$key];
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
}