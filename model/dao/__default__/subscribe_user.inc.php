<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\model\dao\__default__;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class subscribe_user extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"subscribe_user",
            "fields"=>[
                "uId"=>["type"=>tfdao::FIELD_TYPE_INT,"required"=>true],
                "subscribeUId"=>["type"=>tfdao::FIELD_TYPE_INT,"required"=>true]
            ],
            "constraints"=>[
                "default"=>["uId","subscribeUId"]
            ],
            "dataSource"=>"default"
        ]);
    }
}