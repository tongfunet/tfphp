<?php

namespace tfphp\framework\system;

use tfphp\framework\tfphp;
use tfphp\framework\view\tfview;

class tfpage {
    protected tfphp $tfphp;
    protected tfview $view;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $this->view = new tfview($A);
    }
    public function location(string $A3){
        $this->tfphp->getResponse()->location($A3);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
        $this->view->load();
    }
}