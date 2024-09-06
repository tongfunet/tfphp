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
                "title"=>["type"=>tfdao::FIELD_TYPE_STR,"required"=>true]
            ],
            "constraints"=>[
                "default"=>["articleId"]
            ],
            "autoIncrementField"=>"articleId"
        ]);
    }
}