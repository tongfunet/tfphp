<?php

namespace tfphp\controller\api;

use tfphp\framework\system\tfapi;
use tfphp\model\user as userModel;

class user extends tfapi {
    protected function onLoad(){
        $user = new userModel($this->tfphp);
        $users = $user->getUsers();
        $this->responseJsonData($users);
    }
}