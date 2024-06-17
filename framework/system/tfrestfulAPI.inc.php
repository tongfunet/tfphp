<?php

namespace tfphp\framework\system;

use tfphp\tfphp;

class tfrestfulAPI {
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
    protected function response($data, $dataCharset=null){
        $this->tfphp->responseJsonData($data, $dataCharset);
    }
    protected function onGET(){

    }
    protected function onPOST(){

    }
    protected function onPUT(){

    }
    protected function onDELETE(){

    }
    protected function onLoad(){
        switch ($_SERVER["REQUEST_METHOD"]){
            case "GET":
                $this->onGET();
                break;
            case "POST":
                $this->onPOST();
                break;
            case "PUT":
                $this->onPUT();
                break;
            case "DELETE":
                $this->onDELETE();
                break;
        }
    }
    public function load(){
        $this->onLoad();
    }
}