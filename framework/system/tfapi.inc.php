<?php

namespace tfphp\framework\system;

use tfphp\framework\system\server\tfresponse;
use tfphp\framework\tfphp;

class tfapi {
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
    protected function responseJsonData($data, $dataCharset=null){
        $this->tfphp->getResponse()->responseJsonData($data, $dataCharset);
    }
    protected function responseHtmlData($data, string $dataCharset=null){
        $this->tfphp->getResponse()->responseHtmlData($data, $dataCharset);
    }
    protected function responsePlaintextData($data, string $dataCharset=null){
        $this->tfphp->getResponse()->responsePlaintextData($data, $dataCharset);
    }
    protected function location(string $url){
        $this->tfphp->getResponse()->location($url);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
    }
}