<?php

namespace tfphp\framework\system;

use tfphp\framework\system\server\tfresponse;
use tfphp\framework\tfphp;

class tfrestfulAPI {
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
    private function response($C, $D, $A0){
        $A2 = $this->tfphp->getResponse();
        $A2->setDataType($A0);
        $A2->setDataCharset(($D) ? $D : "UTF-8");
        $A2->setData($C);
        $A2->response();
    }
    protected function responseJsonData($C, $D=null){
        $this->tfphp->getResponse()->responseJsonData($C, $D);
    }
    protected function location(string $A5){
        $this->tfphp->getResponse()->location($A5);
    }
    protected function onLoad(){
        $A9 = strtoupper($_SERVER["REQUEST_METHOD"]);
        $AD = $_SERVER["RESTFUL_RESOURCE_FUNCTION"] ;
        if(method_exists($this, "on". $A9)) call_user_func_array([$this, "on". $A9], []);
        else if(method_exists($this, "on_". $AD)) call_user_func_array([$this, "on_". $AD], []);
        else if(method_exists($this, "on". $A9. "_". $AD)) call_user_func_array([$this, "on". $A9. "_". $AD], []);
    }
    public function load(){
        $this->onLoad();
    }
}