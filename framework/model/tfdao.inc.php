<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

abstract class tfdao{
    const FIELD_TYPE_INT = 1;
    const FIELD_TYPE_STR = 2;
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
}