<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system;

use tfphp\framework\tfphp;
use tfphp\framework\view\tfview;

class tfpage extends tfsystem {
    protected tfview $view;
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp);
        $this->view = new tfview($tfphp);
    }
    public function location(string $url){
        $this->tfphp->getResponse()->location($url);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
        $this->view->load();
    }
    public function invoke(Closure $method){
        $boundMethod = Closure::bind($method, $this, get_class($this));
        return $boundMethod();
    }
}