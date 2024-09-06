<?php

namespace tfphp\model\dao;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class userDetail extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"userDetail",
            "fields"=>[
                "userId"=>["type"=>tfdao::FIELD_TYPE_INT,"required"=>true],
                "nickName"=>["type"=>tfdao::FIELD_TYPE_STR],
                "gender"=>["type"=>tfdao::FIELD_TYPE_INT],
                "birth"=>["type"=>tfdao::FIELD_TYPE_STR],
                "description"=>["type"=>tfdao::FIELD_TYPE_STR]
            ],
            "constraints"=>[
                "default"=>["userId"]
            ]
        ]);
    }
}