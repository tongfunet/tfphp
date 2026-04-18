<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\model\dao\__default__;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class user extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"user",
            "fields"=>[
                "uId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "uName"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "uPass"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "uRoleId"=>["type"=>tfdao::FIELD_TYPE_INT,"required"=>true],
                "createDT"=>["type"=>tfdao::FIELD_TYPE_STR],
                "lastLoginDT"=>["type"=>tfdao::FIELD_TYPE_STR]
            ],
            "constraints"=>[
                "default"=>["uId"],
                "uName"=>["uName"]
            ],
            "autoIncrementField"=>"uId",
            "dataSource"=>"default"
        ]);
    }
}