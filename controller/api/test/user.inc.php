<?php

namespace tfphp\controller\api\test;

use tfphp\framework\system\tfapi;
use tfphp\model\user as modelUser;

class user extends tfapi {
    protected function onLoad(){
        $user = new modelUser($this->tfphp);
        try {
            $this->tfphp->responsePlaintextData("");
            $daoUser = $user->getDAOOneToOne("user");

            $ret = $daoUser->insert([
                "userName"=>"鬼谷子叔叔",
                "userPwd"=>md5("123456"),
                "createDT"=>date("Y-m-d H:i:s"),
                "nickName"=>"鬼谷子叔叔",
                "description"=>"这是鬼谷子叔叔的个人介绍"
            ]);
            var_dump("insert", $ret);

            $userInfo = $daoUser->getLastInsert();
            var_dump("getLastInsert", $userInfo);

            $userInfo = $daoUser->select([
                $daoUser->getAutoIncrementField()=>$daoUser->getLastInsertAutoIncrementValue()
            ]);
            var_dump("select by auto-increment field", $userInfo);

            $userInfo = $daoUser->select([
                "userName"=>"鬼谷子叔叔"
            ], "userName");
            var_dump("select by userName", $userInfo);

            $ret = $daoUser->update($userInfo, [
                "updateDT"=>date("Y-m-d H:i:s"),
                "nickName" => "鬼谷子叔叔！",
                "description"=>"这是鬼谷子叔叔的个人介绍！"
            ], "default");
            var_dump("update", $ret);

            $userInfo = $daoUser->select($userInfo, "default");
            var_dump("select after update", $userInfo);

            $ret = $daoUser->delete($userInfo, "default");
            var_dump("delete", $ret);
        }
        catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }
}