<?php

namespace tfphp\framework\system;

use tfphp\framework\system\server\tfresponse;
use tfphp\framework\tfphp;

class tfrestfulAPI {
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
    private function response($data, $dataCharset, $dataType){
        $resp = $this->tfphp->getResponse();
        $resp->setDataType($dataType);
        $resp->setDataCharset(($dataCharset) ? $dataCharset : "UTF-8");
        $resp->setData($data);
        $resp->response();
    }
    protected function responseJsonData($data, $dataCharset=null){
        $this->tfphp->getResponse()->responseJsonData($data, $dataCharset);
    }
    protected function location(string $url){
        $this->tfphp->getResponse()->location($url);
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