<?php

namespace tfphp\framework\controller;

use tfphp\tfphp;

class tfcontroller{
    private tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
}