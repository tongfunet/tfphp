<?php

namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;
use tfphp\model\tffastCRUDController;
use tfphp\model\userCRUD as userCRUDModel;

class userCRUD extends tfrestfulAPI {
    protected function onLoad(){
        $userCRUD = new userCRUDModel($this->tfphp);
        // process
        $my = new tffastCRUDController($this->tfphp);
        $my->select($userCRUD, []);
    }
}