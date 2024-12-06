<?php

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\user;

class users extends tfpage {
    protected function onLoad(){
        $user = new user($this->tfphp);
        $users = $user->getUsers();
        $this->view->setVar("users", $users);
    }
}