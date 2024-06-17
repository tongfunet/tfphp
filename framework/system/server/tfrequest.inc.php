<?php

namespace tfphp\framework\system\server;

use tfphp\tfphp;

class tfrequest{
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
}