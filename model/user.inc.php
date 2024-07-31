<?php

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;
use tfphp\framework\model\tfdaoOneToOne;

class user extends tfmodel{
    public function __construct(tfphp $tfphp){
        $tableUser = new tfdaoSingle($tfphp, [
            "name"=>"user",
            "fields"=>[
                "userId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "userName"=>["type"=>tfdao::FIELD_TYPE_STR],
                "userPwd"=>["type"=>tfdao::FIELD_TYPE_STR],
                "state"=>["type"=>tfdao::FIELD_TYPE_INT],
                "createDT"=>["type"=>tfdao::FIELD_TYPE_STR],
                "updateDT"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["userId"],
                "userName"=>["userName"]
            ],
            "autoIncrementField"=>"userId"
        ]);
        $tableUserDetail = new tfdaoSingle($tfphp, [
            "name"=>"userDetail",
            "fields"=>[
                "userId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "nickName"=>["type"=>tfdao::FIELD_TYPE_STR],
                "gender"=>["type"=>tfdao::FIELD_TYPE_INT],
                "birth"=>["type"=>tfdao::FIELD_TYPE_STR],
                "description"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["userId"]
            ]
        ]);
        parent::__construct($tfphp, [
            "user"=>new tfdaoOneToOne($tfphp, [
                $tableUser,
                $tableUserDetail
            ], [
                "fieldMapping"=>[
                    [
                        "userId"=>"userId"
                    ]
                ]
            ])
        ]);
    }
}