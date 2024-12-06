<?php

namespace tfphp\model\dao\__default__;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class articleClass extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"articleClass",
            "fields"=>[
                "classId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "className"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true]
            ],
            "constraints"=>[
                "default"=>["classId"],
                "u_className"=>["className"]
            ],
            "autoIncrementField"=>"classId",
            "dataSource"=>"default"
        ]);
    }
}