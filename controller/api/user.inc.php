<?php

namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;

class user extends tfrestfulAPI {
    private function responseDemo(string $method){
        $data = [
            "METHOD"=>$method,
            "RESTFUL"=>[
                "RESOURCE"=>[
                    "NAME"=>$_SERVER["RESTFUL_RESOURCE_NAME"],
                    "VALUE"=>$_SERVER["RESTFUL_RESOURCE_VALUE"],
                    "FUNCTION"=>$_SERVER["RESTFUL_RESOURCE_FUNCTION"],
                ]
            ]
        ];
        $this->response($data);
    }
    protected function onGET(){
        $this->responseDemo("GET");
    }
    protected function onPOST(){
        $this->responseDemo("POST");
    }
    protected function onPUT(){
        $this->responseDemo("PUT");
    }
    protected function onDELETE(){
        $this->responseDemo("DELETE");
    }
}