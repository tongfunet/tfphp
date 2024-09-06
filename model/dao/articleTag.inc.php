<?php

namespace tfphp\model\dao;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class articleTag extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"articleTag",
            "fields"=>[
                "tagId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "tagName"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true]
            ],
            "constraints"=>[
                "default"=>["tagId"],
                "u_tagName"=>["tagName"]
            ],
            "autoIncrementField"=>"tagId"
        ]);
    }
}