<?php

namespace tfphp\framework\system;

use tfphp\framework\system\server\tfresponse;
use tfphp\framework\tfphp;

class tfapi {
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
    protected function responseJsonData($A3, $A4=null){
        $this->tfphp->getResponse()->responseJsonData($A3, $A4);
    }
    protected function responseHtmlData($A3, string $A4=null){
        $this->tfphp->getResponse()->responseHtmlData($A3, $A4);
    }
    protected function responsePlaintextData($A3, string $A4=null){
        $this->tfphp->getResponse()->responsePlaintextData($A3, $A4);
    }
    protected function location(string $A6){
        $this->tfphp->getResponse()->location($A6);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
    }
}