<?php

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\user;

class usersWithPages extends tfpage {
    protected function onLoad(){
        $user = new user($this->tfphp);
        $users = $user->getUsersWithPages();
        $this->view->setVar("users", $users);
    }
}