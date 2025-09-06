<?php 

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\user;

class usersWithPages extends tfpage{
    protected function onLoad(){
        $A = new user($this->tfphp);
        $A0 = $A->getUsersWithPages();
        $this->view->setVar("users", $A0);
    }
}