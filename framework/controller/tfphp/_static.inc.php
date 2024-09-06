<?php

namespace tfphp\framework\controller\tfphp;

use tfphp\framework\system\tfapi;

class _static extends tfapi {
    protected function onLoad(){
        $resp = $this->tfphp->getResponse();
        $filepath = $_SERVER["RESOURCE_NAME"];
        $extension = $_SERVER["RESOURCE_EXTENSION"];
        $mimeType = "";
        switch ($extension){
            case "css":
                $mimeType = "text/css";
                break;
            case "js":
                $mimeType = "text/javascript";
                break;
            case "json":
                $mimeType = "application/json";
                break;
            case "xml":
                $mimeType = "text/xml";
                break;
            case "jpg":
            case "jpeg":
                $mimeType = "image/jpeg";
                break;
            case "png":
                $mimeType = "image/png";
                break;
            case "gif":
                $mimeType = "image/gif";
                break;
            case "txt":
                $mimeType = "text/plaintext";
                break;
        }
        if(!file_exists($filepath)){
            $resp->setStatusCode(404);
            $resp->response();
        }
        else if(!$mimeType){
            $resp->setStatusCode(403);
            $resp->response();
        }
        else{
            header("HTTP/1.1 200 OK");
            header("Content-Type: ". $mimeType);
            echo file_get_contents($filepath);
        }
    }
}