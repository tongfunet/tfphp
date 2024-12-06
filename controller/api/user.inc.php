<?php

namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;
use tfphp\model\user as userModel;

class user extends tfrestfulAPI {
    protected function onLoad(){
        $user = new userModel($this->tfphp);
        $resValue = $_SERVER["RESTFUL_RESOURCE_VALUE"];
        $resFunction = $_SERVER["RESTFUL_RESOURCE_FUNCTION"];
        switch ($resFunction){
            case "add":
                $ret = $user->addUser();
                if(!$ret) $this->responseJsonData(["errcode"=>1, "errmsg"=>"fail to add user"]);
                else $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
                break;
            case "modify":
                $ret = $user->modifyUser();
                if(!$ret) $this->responseJsonData(["errcode"=>1, "errmsg"=>"fail to modify user"]);
                else $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
                break;
            case "remove":
                $ret = $user->removeUser();
                if(!$ret) $this->responseJsonData(["errcode"=>1, "errmsg"=>"fail to remove user"]);
                else $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
                break;
            case "list":
                $users = $user->getUsersWithPages();
                $this->responseJsonData($users);
                break;
        }
    }
}