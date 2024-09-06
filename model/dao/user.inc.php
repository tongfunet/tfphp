<?php

namespace tfphp\model\dao;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class user extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"user",
            "fields"=>[
                "userId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "userName"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "userPwd"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "state"=>["type"=>tfdao::FIELD_TYPE_INT],
                "createDT"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "updateDT"=>["type"=>tfdao::FIELD_TYPE_STR]
            ],
            "constraints"=>[
                "default"=>["userId"],
                "u_userName"=>["userName"]
            ],
            "autoIncrementField"=>"userId"
        ]);
    }
}