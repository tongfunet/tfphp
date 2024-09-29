<?php

namespace tfphp\model\dao;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class article_tag extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"article_tag",
            "fields"=>[
                "articleId"=>["type"=>tfdao::FIELD_TYPE_INT,"required"=>true],
                "tagId"=>["type"=>tfdao::FIELD_TYPE_INT,"required"=>true]
            ],
            "constraints"=>[
                "default"=>["articleId","tagId"]
            ]
        ]);
    }
}