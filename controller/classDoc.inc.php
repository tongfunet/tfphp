<?php 

namespace tfphp\controller;

use tfphp\framework\system\tfpage;

class classDoc extends tfpage{
    protected function onLoad(){
        $this->view->setVar("args", $this->tfphp->getRequest()->server()->pathArgs());
    }
}