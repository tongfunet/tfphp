<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\controller\tfphp;

use tfphp\framework\system\tfapi;

class _static extends tfapi{
    protected function onLoad(){
        $resp = $this->tfphp->getResponse();
        $resName = $_SERVER["RESOURCE_NAME"];
        $resExt = $_SERVER["RESOURCE_EXTENSION"];
        $contentType = "";
        switch ($resExt){
            case "css":
                $contentType = "text/css";
                break;
            case "js":
                $contentType = "text/javascript";
                break;
            case "json":
                $contentType = "application/json";
                break;
            case "xml":
                $contentType = "text/xml";
                break;
            case "jpg":
            case "jpeg":
                $contentType = "image/jpeg";
                break;
            case "png":
                $contentType = "image/png";
                break;
            case "gif":
                $contentType = "image/gif";
                break;
            case "txt":
                $contentType = "text/plaintext";
                break;
            case "ico":
                $contentType = "image/x-icon";
                break;
        }
        if(!file_exists($resName)){
            $resp->setStatusCode(404);
            $resp->response();
        }
        else if(!$contentType){
            $resp->setStatusCode(403);
            $resp->response();
        }
        else{
            header("HTTP/1.1 200 OK");
            header("Content-Type: ". $contentType);
            echo file_get_contents($resName);
        }
    }
}