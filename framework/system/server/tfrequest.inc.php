<?php

namespace tfphp\framework\system\server;

use tfphp\framework\tfphp;

class tfrequest{
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
}