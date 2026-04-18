<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\model\dao\__default__;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class user_detail extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"user_detail",
            "fields"=>[
                "uId"=>["type"=>tfdao::FIELD_TYPE_INT,"required"=>true],
                "uRealName"=>["type"=>tfdao::FIELD_TYPE_STR],
                "uGender"=>["type"=>tfdao::FIELD_TYPE_INT],
                "uDescript"=>["type"=>tfdao::FIELD_TYPE_STR]
            ],
            "constraints"=>[
                "default"=>["uId"]
            ],
            "dataSource"=>"default"
        ]);
    }
}