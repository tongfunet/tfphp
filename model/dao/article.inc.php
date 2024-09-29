<?php

namespace tfphp\model\dao;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class article extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"article",
            "fields"=>[
                "articleId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "classId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "title"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "content"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "createDT"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true],
                "updateDT"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["articleId"]
            ],
            "autoIncrementField"=>"articleId"
        ]);
    }
}