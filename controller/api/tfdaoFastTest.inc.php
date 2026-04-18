<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;
use tfphp\model\tfdaoFastTest as mTFDAOFastTest;

class tfdaoFastTest extends tfrestfulAPI {
    private array $assertResults;
    private function assertResult($result, $assertResult, $logMessage){
        if($result === $assertResult) $this->assertResults[] = [$logMessage=>true];
        else $this->assertResults[] = [$logMessage=>false];
        return $result;
    }
    protected function onLoad(){
        $this->assertResults = [];
        $mTFDAOFastTest = new mTFDAOFastTest($this->tfphp);

        // 清理数据
        $ds = $this->tfphp->getDataSource();
        $this->assertResult(
            $ds->execute("truncate table role", []), true, "truncate table role"
        );
        $this->assertResult(
            $ds->execute("truncate table user", []), true, "truncate table user"
        );
        $this->assertResult(
            $ds->execute("truncate table user_detail", []), true, "truncate table user_detail"
        );
        $this->assertResult(
            $ds->execute("truncate table subscribe_user", []), true, "truncate table subscribe_user"
        );

        // 插入三个角色，获取第二个的自增列ID
        $tRole = $mTFDAOFastTest->getSG("role");
        if(!$tRole->selectByUnique(["普通用户"], "rName")){
            $this->assertResult(
                $tRole->insert([
                    "rName"=>"普通用户",
                    "rDescript"=>"普通用户",
                    "createDT"=>date("Y-m-d H:i:s")
                ]), true, "insert role '普通用户'"
            );
        }
        $roleGJId = 0;
        if(!$tRole->selectByUnique(["高级用户"], "rName")){
            $this->assertResult(
                $tRole->insert([
                    "rName"=>"高级用户",
                    "rDescript"=>"高级用户",
                    "createDT"=>date("Y-m-d H:i:s")
                ]), true, "insert role '高级用户'"
            );
            $roleGJId = $tRole->getLastInsertAutoIncrementValue();
        }
        if(!$tRole->selectByUnique(["管理员"], "rName")){
            $this->assertResult(
                $tRole->insert([
                    "rName"=>"管理员",
                    "rDescript"=>"管理员",
                    "createDT"=>date("Y-m-d H:i:s")
                ]), true, "insert role '管理员'"
            );
        }

        // 插入三个用户，获取第二个的自增列ID
        $tFullUser = $mTFDAOFastTest->getO2O("fullUser");
        $roleGLY = $tRole->selectByUnique(["管理员"], "rName");
        if(!$roleGLY){
            throw new \Exception("找不到角色“管理员”");
        }
        if(!$tFullUser->selectByUnique(["福哥"], "uName")){
            $this->assertResult(
                $tFullUser->insert([
                    "uName"=>"福哥",
                    "uPass"=>md5("123456"),
                    "uRoleId"=>$roleGLY["rId"],
                    "createDT"=>date("Y-m-d H:i:s"),
                    "uRealName"=>"同福",
                    "uGender"=>0,
                    "uDescript"=>"福哥就是同福啦！"
                ]), true, "insert user '福哥'"
            );
        }
        $userGGZSSId = 0;
        if(!$tFullUser->selectByUnique(["鬼谷子叔叔"], "uName")){
            $this->assertResult(
                $tFullUser->insert([
                    "uName"=>"鬼谷子叔叔",
                    "uPass"=>md5("123456"),
                    "uRoleId"=>$roleGLY["rId"],
                    "createDT"=>date("Y-m-d H:i:s"),
                    "uRealName"=>"鬼谷子叔叔",
                    "uGender"=>1,
                    "uDescript"=>"鬼谷子叔叔也是同福啦！"
                ]), true, "insert user '鬼谷子叔叔'"
            );
            $userGGZSSId = $tFullUser->getLastInsertAutoIncrementValue();
        }
        $rolePT = $tRole->selectByUnique(["普通用户"], "rName");
        if(!$rolePT){
            throw new \Exception("找不到角色“普通用户”");
        }
        if(!$tFullUser->selectByUnique(["测试用户"], "uName")){
            $this->assertResult(
                $tFullUser->insert([
                    "uName"=>"测试用户",
                    "uPass"=>md5("123456"),
                    "uRoleId"=>$rolePT["rId"],
                    "createDT"=>date("Y-m-d H:i:s"),
                    "uRealName"=>"测试用户",
                    "uGender"=>2,
                    "uDescript"=>"测试用户不认识！"
                ]), true, "insert user '测试用户'"
            );
        }

        // 绑定和解绑以及全部取关
        $tSubscribeUser = $mTFDAOFastTest->getM2M("subscribeUser");
        $userFG = $tFullUser->select(["uName"=>"福哥"]);
        $userGGZSS = $tFullUser->selectByPrimary([$userGGZSSId]);
        $userTest = $tFullUser->selectByUnique(["测试用户"], "uName");
        if($userFG && $userGGZSS && $userTest){
            $this->assertResult(
                $tSubscribeUser->bind([
                    $userFG
                ], [
                    $userGGZSS,
                    $userTest
                ]), true, "user '福哥' subscribe users '鬼谷子叔叔', '测试用户'"
            );
            $this->assertResult(
                $tSubscribeUser->unbind([
                    ["uId"=>$userFG["uId"]]
                ], [
                    ["uId"=>$userTest["uId"]]
                ]), true, "user '福哥' unsubscribe user '测试用户'"
            );
            $this->assertResult(
                $tSubscribeUser->replace([
                    ["uId"=>$userFG["uId"]]
                ], []), true, "user '福哥' unsubscribe all users"
            );
        }

        // 测试Single的更新和删除
        $this->assertResult(
            $tRole->update(["rDescript"=>"这是普通用户"], ["rName"=>"普通用户"]), true, "update role '普通用户'"
        );
        $this->assertResult(
            $tRole->updateByPrimary(["rDescript"=>"这是高级用户"], [$roleGJId]), true, "update role '高级用户' by primary key"
        );
        $this->assertResult(
            $tRole->updateByUnique(["rDescript"=>"这是管理员"], ["管理员"], "rName"), true, "update role '管理员' by unique key"
        );
        $this->assertResult(
            $tRole->delete(["rName"=>"普通用户"]), true, "delete role '普通用户'"
        );
        $this->assertResult(
            $tRole->deleteByPrimary([$roleGJId]), true, "delete role '高级用户' by primary key"
        );
        $this->assertResult(
            $tRole->deleteByUnique(["管理员"], "rName"), true, "delete role '管理员' by unique key"
        );

        // 测试OneToOne的更新和删除
        $this->assertResult(
            $tFullUser->update(["uDescript"=>"福哥就是他！"], ["uName"=>"福哥"]), true, "update user '福哥'"
        );
        $this->assertResult(
            $tFullUser->updateByPrimary(["uDescript"=>"鬼谷子叔叔也是他！"], [$userGGZSSId]), true, "update user '鬼谷子叔叔' by primary key"
        );
        $this->assertResult(
            $tFullUser->updateByUnique(["uDescript"=>"测试用户不是他！"], ["测试用户"], "uName"), true, "update user '测试用户' by unique key"
        );
        $this->assertResult(
            $tFullUser->delete(["uName"=>"福哥"]), true, "delete user '福哥'"
        );
        $this->assertResult(
            $tFullUser->deleteByPrimary([$userGGZSSId]), true, "delete user '鬼谷子叔叔' by primary key"
        );
        $this->assertResult(
            $tFullUser->deleteByUnique(["测试用户"], "uName"), true, "delete user '测试用户' by unique key"
        );

        $this->JSONData($this->assertResults);
    }
}