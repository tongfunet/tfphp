<?php 

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;

class user extends tfmodel{
    public function __construct(tfphp $A){
        parent::__construct($A);
        $this->setDAOOneToOne("fullUser", ["user", "userDetail"], [
            ["mapping"=>["userId"=>"userId"]]
        ]);
    }
    private function A5(): ?array{
        $AB = $this->getSG("user");
        $AC = $AB->selectAll(["state"=>1]);
        if($AC){
            if(count($AC) > 1){
                $B2 = rand(0, count($AC)-1);
                return $AC[$B2];
            }
            return $AC[0];
        }
        return null;
    }
    public function tfdaoSingleCRUD(): array{
        $AB = $this->getSG("user");
        $B7 = $AB->getTFDO();
        $B7->execute3("truncate table user", []);
        $BC = [];
        $BC[] = ["tfdaoSingle", "CRUD"];
        $BC[] = ["insert tongfu", $AB->insert([
            "userName"=>"tongfu",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1
        ])];
        $BD = $AB->getLastInsertData()["userId"];
        $BC[] = ["select user with id 1", $AB->keySelect([$BD])];
        $BC[] = ["select many users with state 1", $AB->selectMany(["state"=>1], 0, 10)];
        $BC[] = ["select all users with state 1", $AB->selectAll(["state"=>1])];
        $BC[] = ["update user with id 1", $AB->keyUpdate([$BD], ["userName"=>"fuge"])];
        $BC[] = ["select user with id 1 after update", $AB->keySelect([$BD])];
        $BC[] = ["delete user with id 1", $AB->keyDelete([$BD])];
        $BC[] = ["select user with id 1 after delete", $AB->keySelect([$BD])];
        return $BC;
    }
    public function tfdaoOneToOneCRUD(): array{
        $C3 = $this->getO2O("fullUser");
        $B7 = $this->tfphp->getDataSource();
        $B7->execute3("truncate table user", []);
        $B7->execute3("truncate table userDetail", []);
        $BC = [];
        $BC[] = ["tfdaoOneToOne", "CRUD"];
        $BC[] = ["insert tongfu", $C3->insert([
            "userName"=>"tongfu",
            "userPwd"=>md5("123456"),
            "createDT"=>date("Y-m-d H:i:s"),
            "state"=>1,
            "nickName"=>"同福",
            "gender"=>1,
            "birth"=>"2006-06-06"
        ])];
        $BD = $C3->getLastInsertData()["userId"];
        $BC[] = ["select full user with id 1", $C3->keySelect([$BD])];
        $BC[] = ["update full user with id 1", $C3->keyUpdate([$BD], ["userName"=>"fuge", "description"=>"同福就是鬼谷子叔叔"])];
        $BC[] = ["select full user with id 1 after update", $C3->keySelect([$BD])];
        $BC[] = ["delete full user with id 1", $C3->keyDelete([$BD])];
        $BC[] = ["select full user with id 1 after delete", $C3->keySelect([$BD])];
        return $BC;
    }
    public function addUser(): bool{
        $C3 = $this->getO2O("fullUser");
        $C9 = "user". date("YmdHis");
        $CF = md5(time());
        $D2 = "用户". date("YmdHis");
        if($C3->select(["userName"=>$C9])){
            return false;
        }
        if(!$C3->insert([
            "userName"=>$C9,
            "userPwd"=>$CF,
            "state"=>1,
            "createDT"=>date("Y-m-d H:i:s"),
            "nickName"=>$D2,
            "gender"=>rand(0, 1),
            "birth"=>"2008-08-08"
        ])){
            return false;
        }
        return true;
    }
    public function modifyUser(): bool{
        $C3 = $this->getO2O("fullUser");
        $D8 = $this->A5();
        if(!$D8){
            return false;
        }
        if(!$C3->keyUpdate([$D8["userId"]], [
            "description"=>"修改于". date("Y-m-d H:i:s"),
            "updateDT"=>date("Y-m-d H:i:s")
        ])){
            return false;
        }
        return true;
    }
    public function removeUser(): bool{
        $C3 = $this->getO2O("fullUser");
        $D8 = $this->A5();
        if(!$D8){
            return false;
        }
        if(!$C3->keyDelete([$D8["userId"]])){
            return false;
        }
        return true;
    }
    public function getUsers(): array{
        $B7 = $this->tfphp->getDataSource();
        $D9 = $B7->fetchAll3("select * from user u 
                inner join userDetail ud 
                on u.userId = ud.userId
                order by u.userId desc", []);
        if($D9 === null){
            return [];
        }
        return $D9;
    }
    public function getUsersWithPages(): array{
        $B7 = $this->tfphp->getDataSource();
        $DD = $this->tfphp->getRequest()->get();
        $DE = 5;
        $E4 = ($DD->get("pn")) ? $DD->get("pn") : 1;
        // process
        $E8 = "select * from user u 
            inner join userDetail ud 
            on u.userId = ud.userId
            order by u.userId desc";
        $E9 = $B7->fetchOne3("select count(*) as cc from (". preg_replace("/select.*from/i", "select 1 from", $E8). ") as tt", []);
        if($E9 === null){
            return ["data"=>[]];
        }
        $EC = $B7->makePagination($E9["cc"], $DE, $E4);
        $D9 = $B7->fetchMany3($E8, [], ($E4-1)*$DE, $DE);
        if($D9 === null){
            return ["data"=>[]];
        }
        return [
            "data"=>$D9,
            "page"=>$EC,
        ];
    }
}