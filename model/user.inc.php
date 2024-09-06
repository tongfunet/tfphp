<?php

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;
use tfphp\framework\model\tfdaoOneToOne;

class user extends tfmodel{
    public function __construct(tfphp $tfphp){
        $tableUser = new dao\user($tfphp);
        $tableUserDetail = new dao\userDetail($tfphp);
        parent::__construct($tfphp, [
            "user"=>new tfdaoOneToOne($tfphp, [
                $tableUser,
                $tableUserDetail
            ], [
                "fieldMapping"=>[["userId"=>"userId"]]
            ])
        ]);
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
}