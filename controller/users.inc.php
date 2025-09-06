<?php 

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\user;

class users extends tfpage{
    protected function onLoad(){
        $A = new user($this->tfphp);
        $A1 = $A->getUsers();
        $this->view->setVar("users", $A1);
    }
}