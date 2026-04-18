<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system;

use tfphp\framework\tfphp;

class tfrestfulAPI extends tfsystem {
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp);
    }
    protected function responseJSONData($data, $dataCharset=null){
        $this->tfphp->getResponse()->responseJSONData($data, $dataCharset);
    }
    protected function JSONData($data, $dataCharset=null){
        $this->responseJSONData($data, $dataCharset);
    }
    protected function JSON(int $statusCode, $data, string $dataCharset=null){
        $this->tfphp->getResponse()->JSON($statusCode, $data, $dataCharset);
    }
    protected function location(string $url){
        $this->tfphp->getResponse()->location($url);
    }
    protected function onLoad(){
        $reqMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $resFunction = $this->tfphp->getRequest()->getResourceFunction();
        if($resFunction != "" && method_exists($this, "on_". $resFunction)) call_user_func_array([$this, "on_". $resFunction], []);
        else if($resFunction != "" && method_exists($this, "on". $reqMethod. "_". $resFunction)) call_user_func_array([$this, "on". $reqMethod. "_". $resFunction], []);
        else if(method_exists($this, "on". $reqMethod)) call_user_func_array([$this, "on". $reqMethod], []);
        else $this->onLoadCustom();
    }
    protected function onLoadCustom(){

    }
    public function load(){
        $this->onLoad();
    }
}