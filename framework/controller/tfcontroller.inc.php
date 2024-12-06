<?php

namespace tfphp\framework\controller;

use tfphp\framework\tfphp;

class tfcontroller{
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
}