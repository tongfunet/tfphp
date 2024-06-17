<?php

namespace tfphp\framework\system;

use tfphp\tfphp;
use tfphp\framework\view\tfview;

class tfpage {
    protected tfphp $tfphp;
    protected tfview $view;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->view = new tfview($tfphp);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
        $this->view->load();
    }
}