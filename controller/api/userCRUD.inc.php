<?php 

namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;
use tfphp\controller\tffastCRUDController;
use tfphp\model\userCRUD as userCRUDModel;

class userCRUD extends tfrestfulAPI{
    protected function onLoad(){
        $A = new userCRUDModel($this->tfphp);
        // process
        $C = new tffastCRUDController($this->tfphp);
        $C->select($A, []);
    }
}