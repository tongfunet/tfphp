<?php 

namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;
use tfphp\model\user as userModel;

class user extends tfrestfulAPI{
    protected function onLoad(){
        $A = new userModel($this->tfphp);
        $A3 = $_SERVER["RESTFUL_RESOURCE_VALUE"];
        $A7 = $_SERVER["RESTFUL_RESOURCE_FUNCTION"];
        switch ($A7){
            case "add":
                $AA = $A->addUser();
                if(!$AA) $this->responseJsonData(["errcode"=>1, "errmsg"=>"fail to add user"]);
                else $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
                break;
            case "modify":
                $AA = $A->modifyUser();
                if(!$AA) $this->responseJsonData(["errcode"=>1, "errmsg"=>"fail to modify user"]);
                else $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
                break;
            case "remove":
                $AA = $A->removeUser();
                if(!$AA) $this->responseJsonData(["errcode"=>1, "errmsg"=>"fail to remove user"]);
                else $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
                break;
            case "list":
                $AF = $A->getUsersWithPages();
                $this->responseJsonData($AF);
                break;
        }
    }
}