<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system;

use tfphp\framework\tfphp;

class tfapi extends tfsystem {
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
    protected function responseHTMLData($data, string $dataCharset=null){
        $this->tfphp->getResponse()->responseHTMLData($data, $dataCharset);
    }
    protected function HTMLData($data, string $dataCharset=null){
        $this->responseHTMLData($data, $dataCharset);
    }
    protected function HTML(int $statusCode, $data, string $dataCharset=null){
        $this->tfphp->getResponse()->HTML($statusCode, $data, $dataCharset);
    }
    protected function responsePlainTextData($data, string $dataCharset=null){
        $this->tfphp->getResponse()->responsePlainTextData($data, $dataCharset);
    }
    protected function PlainTextData($data, string $dataCharset=null){
        $this->responsePlainTextData($data, $dataCharset);
    }
    protected function PlainText(int $statusCode, $data, string $dataCharset=null){
        $this->tfphp->getResponse()->PlainText($statusCode, $data, $dataCharset);
    }
    protected function location(string $url){
        $this->tfphp->getResponse()->location($url);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
    }
}