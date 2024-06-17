<?php

namespace tfphp\controller;

use tfphp\framework\system\tfpage;

class index extends tfpage {
    protected function onLoad(){
        $this->view->setVar("title", "this is a page");
        $this->view->setVar("page", [
            "h1"=>"this is a page",
            "p"=>"it is a full HTML page.",
        ]);
    }
}