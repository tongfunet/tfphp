<?php

namespace tfphp\framework\system;

use tfphp\framework\tfphp;
use tfphp\framework\view\tfview;

class tfpage {
    protected tfphp $tfphp;
    protected tfview $view;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->view = new tfview($tfphp);
    }
    public function location(string $url, bool $stopScript=true){
        $this->tfphp->location($url, $stopScript);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
        $this->view->load();
    }
}