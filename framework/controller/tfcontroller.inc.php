<?php

namespace tfphp\framework\controller;

use tfphp\framework\tfphp;

class tfcontroller{
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
}