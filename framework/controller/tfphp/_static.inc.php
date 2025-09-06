<?php 

namespace tfphp\framework\controller\tfphp;

use tfphp\framework\system\tfapi;

class _static extends tfapi{
    protected function onLoad(){
        $A = $this->tfphp->getResponse();
        $A0 = $_SERVER["RESOURCE_NAME"];
        $A2 = $_SERVER["RESOURCE_EXTENSION"];
        $A8 = "";
        switch ($A2){
            case "css":
                $A8 = "text/css";
                break;
            case "js":
                $A8 = "text/javascript";
                break;
            case "json":
                $A8 = "application/json";
                break;
            case "xml":
                $A8 = "text/xml";
                break;
            case "jpg":
            case "jpeg":
                $A8 = "image/jpeg";
                break;
            case "png":
                $A8 = "image/png";
                break;
            case "gif":
                $A8 = "image/gif";
                break;
            case "txt":
                $A8 = "text/plaintext";
                break;
            case "ico":
                $A8 = "image/x-icon";
                break;
        }
        if(!file_exists($A0)){
            $A->setStatusCode(404);
            $A->response();
        }
        else if(!$A8){
            $A->setStatusCode(403);
            $A->response();
        }
        else{
            header("HTTP/1.1 200 OK");
            header("Content-Type: ". $A8);
            echo file_get_contents($A0);
        }
    }
}