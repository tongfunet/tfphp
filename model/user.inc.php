<?php

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;

class user extends tfmodel{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp);
        $this->setDAOOneToOne("fullUser", ["user", "userDetail"], [["userId"=>"userId"]]);
    }
    private function getRandomUser(): ?array{
        $user = $this->getSG("user");
        $allUsers = $user->selectAll(["state"=>1]);
        if($allUsers){
            if(count($allUsers) > 1){
                $index = rand(0, count($allUsers)-1);
                return $allUsers[$index];
            }
            return $allUsers[0];
        }
        return null;
    }
    public function tfdaoSingleCRUD(): array{
        $user = $this->getSG("user");
        $ds = $user->getTFDO();
        $ds->execute3("truncate table user", []);
        $results = [];
        $results[] = ["tfdaoSingle", "CRUD"];
        $results[] = ["insert tongfu", $user->insert([
            "userName"=>"tongfu",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1
        ])];
        $results[] = ["insert fuge", $user->insert([
            "userName"=>"fuge",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1
        ])];
        $results[] = ["select user with id 1", $user->select(["userId"=>1])];
        try {
            $user->update(["userId"=>1], ["userName"=>"fuge"]);
        }
        catch (\Exception $e){
            $results[] = ["fail to update user to fuge with id 1", $e->getMessage()];
        }
        $results[] = ["select user with id 1", $user->select(["userId"=>1])];
        $results[] = ["update user to tongfu2 with id 1", $user->update(["userId"=>1], ["userName"=>"tongfu2"])];
        $results[] = ["select user with id 1", $user->select(["userId"=>1])];
        $results[] = ["select many users with state 1", $user->selectMany(["state"=>1])];
        $results[] = ["select all users with state 1", $user->selectAll(["state"=>1])];
        $results[] = ["delete user with id 1", $user->delete(["userId"=>1])];
        $results[] = ["select user with id 1", $user->select(["userId"=>1])];
        $results[] = ["delete user with id 2", $user->delete(["userId"=>2])];
        $results[] = ["select user with id 2", $user->select(["userId"=>2])];
        $results[] = ["select many users with state 1", $user->selectMany(["state"=>1])];
        $results[] = ["select all users with state 1", $user->selectAll(["state"=>1])];
        return $results;
    }
    public function tfdaoSingleConstraintCRUD(): array{
        $user = $this->getSG("user");
        $ds = $user->getTFDO();
        $ds->execute3("truncate table user", []);
        $results = [];
        $results[] = ["tfdaoSingleConstraint", "CRUD"];
        $results[] = ["insert tongfu", $user->insert([
            "userName"=>"tongfu",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1
        ])];
        $results[] = ["insert fuge", $user->insert([
            "userName"=>"fuge",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1
        ])];
        $results[] = ["select user with id 1", $user->keySelect([1])];
        try {
            $user->keyUpdate([1], ["userName"=>"fuge"]);
        }
        catch (\Exception $e){
            $results[] = ["fail to update user to fuge with id 1", $e->getMessage()];
        }
        $results[] = ["select user with id 1", $user->keySelect([1])];
        $results[] = ["update user to tongfu2 with id 1", $user->keyUpdate([1], ["userName"=>"tongfu2"])];
        $results[] = ["select user with id 1", $user->keySelect([1])];
        $results[] = ["select many users with id 1", $user->keySelectMany([1])];
        $results[] = ["select all users with id 2", $user->keySelectAll([2])];
        $results[] = ["delete user with id 1", $user->keyDelete([1])];
        $results[] = ["select user with id 1", $user->keySelect([1])];
        $results[] = ["delete user with id 2", $user->keyDelete([2])];
        $results[] = ["select user with id 2", $user->keySelect([2])];
        $results[] = ["select many users with id 1", $user->keySelectMany([1])];
        $results[] = ["select all users with id 2", $user->keySelectAll([2])];
        return $results;
    }
    public function tfdaoOneToOneCRUD(): array{
        $fullUser = $this->getO2O("fullUser");
        $ds = $this->tfphp->getDataSource();
        $ds->execute3("truncate table user", []);
        $ds->execute3("truncate table userDetail", []);
        $results = [];
        $results[] = ["tfdaoOneToOne", "CRUD"];
        $results[] = ["insert tongfu", $fullUser->insert([
            "userName"=>"tongfu",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1,
            "nickName"=>"同福",
            "gender"=>1,
            "birth"=>"2006-06-06"
        ])];
        $results[] = ["insert fuge", $fullUser->insert([
            "userName"=>"fuge",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1,
            "nickName"=>"福哥",
            "gender"=>1,
            "birth"=>"2008-08-08"
        ])];
        $results[] = ["select user with id 1", $fullUser->select(["userId"=>1])];
        try {
            $fullUser->update(["userId"=>1], ["userName"=>"fuge"]);
        }
        catch (\Exception $e){
            $results[] = ["fail to update user to fuge with id 1", $e->getMessage()];
        }
        $results[] = ["select user with id 1", $fullUser->select(["userId"=>1])];
        $results[] = ["update user to tongfu2 with id 1", $fullUser->update(["userId"=>1], ["userName"=>"tongfu2"])];
        $results[] = ["select user with id 1", $fullUser->select(["userId"=>1])];
        $results[] = ["delete user with id 1", $fullUser->delete(["userId"=>1])];
        $results[] = ["select user with id 1", $fullUser->select(["userId"=>1])];
        $results[] = ["delete user with id 2", $fullUser->delete(["userId"=>2])];
        $results[] = ["select user with id 2", $fullUser->select(["userId"=>2])];
        return $results;
    }
    public function tfdaoOneToOneConstraintCRUD(): array{
        $fullUser = $this->getO2O("fullUser");
        $ds = $this->tfphp->getDataSource();
        $ds->execute3("truncate table user", []);
        $ds->execute3("truncate table userDetail", []);
        $results = [];
        $results[] = ["tfdaoOneToOneConstraint", "CRUD"];
        $results[] = ["insert tongfu", $fullUser->insert([
            "userName"=>"tongfu",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1,
            "nickName"=>"同福",
            "gender"=>1,
            "birth"=>"2006-06-06"
        ])];
        $results[] = ["insert fuge", $fullUser->insert([
            "userName"=>"fuge",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1,
            "nickName"=>"福哥",
            "gender"=>1,
            "birth"=>"2008-08-08"
        ])];
        $results[] = ["select user with id 1", $fullUser->constraintSelect([1])];
        try {
            $fullUser->constraintUpdate([1], ["userName"=>"fuge"]);
        }
        catch (\Exception $e){
            $results[] = ["fail to update user to fuge with id 1", $e->getMessage()];
        }
        $results[] = ["select user with id 1", $fullUser->constraintSelect([1])];
        $results[] = ["update user to tongfu2 with id 1", $fullUser->constraintUpdate([1], ["userName"=>"tongfu2"])];
        $results[] = ["select user with id 1", $fullUser->constraintSelect([1])];
        $results[] = ["delete user with id 1", $fullUser->constraintDelete([1])];
        $results[] = ["select user with id 1", $fullUser->constraintSelect([1])];
        $results[] = ["delete user with id 2", $fullUser->constraintDelete([2])];
        $results[] = ["select user with id 2", $fullUser->constraintSelect([2])];
        return $results;
    }
    public function getUsers(): array{
        $ds = $this->tfphp->getDataSource();
        $users = $ds->fetchAll3("select * from user u 
                inner join userDetail ud 
                on u.userId = ud.userId
                order by u.userId desc", []);
        if($users === null){
            return [];
        }
        return $users;
    }
    public function addUser(): bool{
        $fullUser = $this->getO2O("fullUser");
        $userName = "user". date("YmdHis");
        $userPwd = md5(time());
        $nickName = "用户". date("YmdHis");
        if($fullUser->select(["userName"=>$userName])){
            return false;
        }
        if(!$fullUser->insert([
            "userName"=>$userName,
            "userPwd"=>$userPwd,
            "state"=>1,
            "createDT"=>date("Y-m-d H:i:s"),
            "nickName"=>$nickName,
            "gender"=>rand(0, 1),
            "birth"=>"2008-08-08"
        ])){
            return false;
        }
        return true;
    }
    public function modifyUser(): bool{
        $fullUser = $this->getO2O("fullUser");
        $myUser = $this->getRandomUser();
        if(!$myUser){
            return false;
        }
        if(!$fullUser->keyUpdate([$myUser["userId"]], [
            "description"=>"修改于". date("Y-m-d H:i:s"),
            "updateDT"=>date("Y-m-d H:i:s")
        ])){
            return false;
        }
        return true;
    }
    public function removeUser(): bool{
        $fullUser = $this->getO2O("fullUser");
        $myUser = $this->getRandomUser();
        if(!$myUser){
            return false;
        }
        if(!$fullUser->keyDelete([$myUser["userId"]])){
            return false;
        }
        return true;
    }
    public function getUsersWithPages(): array{
        $ds = $this->tfphp->getDataSource();
        $pp = 10;
        $cp = ($_GET["pn"]) ? $_GET["pn"] : 1;
        // process
        $sql = "select * from user u 
            inner join userDetail ud 
            on u.userId = ud.userId
            order by u.userId desc";
        $totalUsers = $ds->fetchOne3("select count(*) as cc from (". preg_replace("/select.*from/i", "select 1 from", $sql). ") as tt", []);
        if($totalUsers === null){
            return ["data"=>[]];
        }
        $pages = $ds->makePagination($totalUsers["cc"], $pp, $cp);
        $users = $ds->fetchMany3($sql, [], ($cp-1)*$pp, $pp);
        if($users === null){
            return ["data"=>[]];
        }
        return [
            "data"=>$users,
            "page"=>$pages,
        ];
    }
}