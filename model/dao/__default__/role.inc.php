<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\model\dao\__default__;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class role extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"role",
            "fields"=>[
                "rId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "rName"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "rDescript"=>["type"=>tfdao::FIELD_TYPE_STR],
                "createDT"=>["type"=>tfdao::FIELD_TYPE_STR]
            ],
            "constraints"=>[
                "default"=>["rId"],
                "rName"=>["rName"]
            ],
            "autoIncrementField"=>"rId",
            "dataSource"=>"default"
        ]);
    }
}