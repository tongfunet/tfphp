<?php

namespace tfphp\framework\system;

use tfphp\tfphp;

class tfapi {
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
    protected function responseJsonData($data, $dataCharset=null){
        $this->tfphp->responseJsonData($data, $dataCharset);
    }
    public function responseHtmlData($data, string $dataCharset=null){
        $this->tfphp->responseHtmlData($data, $dataCharset);
    }
    public function responsePlaintextData($data, string $dataCharset=null){
        $this->tfphp->responsePlaintextData($data, $dataCharset);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
    }
}