<?php

namespace tfphp\framework\system;

use tfphp\framework\tfphp;

class tfapi {
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
    protected function responseJsonData($data, $dataCharset=null, bool $stopScript=true){
        $this->tfphp->responseJsonData($data, $dataCharset, $stopScript);
    }
    public function responseHtmlData($data, string $dataCharset=null, bool $stopScript=true){
        $this->tfphp->responseHtmlData($data, $dataCharset, $stopScript);
    }
    public function responsePlaintextData($data, string $dataCharset=null, bool $stopScript=true){
        $this->tfphp->responsePlaintextData($data, $dataCharset, $stopScript);
    }
    public function location(string $url, bool $stopScript=true){
        $this->tfphp->location($url, $stopScript);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
    }
}