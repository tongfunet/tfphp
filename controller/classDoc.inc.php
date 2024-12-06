<?php

namespace tfphp\controller;

use tfphp\framework\system\tfpage;

class classDoc extends tfpage {
    protected function onLoad(){
        $this->view->setVar("argv", $_SERVER["PATH_ARGV"]);
    }
}