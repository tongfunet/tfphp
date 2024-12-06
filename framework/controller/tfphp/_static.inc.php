<?php

namespace tfphp\framework\controller\tfphp;

use tfphp\framework\system\tfapi;

class _static extends tfapi{
    protected function onLoad(){
        $A = $this->tfphp->getResponse();
        $A3 = $_SERVER["RESOURCE_NAME"] ;
        $A7 = $_SERVER["RESOURCE_EXTENSION"] ;
        $AA = "" ;
        switch ($A7){
            case "css":
                $AA = "text/css";
                break;
            case "js":
                $AA = "text/javascript" ;
                break;
            case "json":
                $AA = "application/json" ;
                break;
            case "xml":
                $AA = "text/xml" ;
                break;
            case "jpg":
            case "jpeg":
                $AA = "image/jpeg" ;
                break;
            case "png":
                $AA = "image/png" ;
                break;
            case "gif":
                $AA = "image/gif" ;
                break;
            case "txt":
                $AA = "text/plaintext" ;
                break;
            case "ico":
                $AA = "image/x-icon" ;
                break;
        }
        if(!file_exists($A3)){
            $A->setStatusCode(404);
            $A->response();
        }
        else if(!$AA){
            $A->setStatusCode(403);
            $A->response();
        }
        else{
            header("HTTP/1.1 200 OK");
            header("Content-Type: ". $AA);
            echo file_get_contents($A3);
        }
    }
}