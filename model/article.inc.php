<?php

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;
use tfphp\framework\model\tfdaoOneToMany;
use tfphp\framework\model\tfdaoManyToMany;

class article extends tfmodel{
    public function __construct(tfphp $tfphp){
        $tableArticle = new tfdaoSingle($tfphp, [
            "name"=>"article",
            "fields"=>[
                "articleId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "classId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "title"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["articleId"],
                "classId"=>["classId"]
            ],
            "autoIncrementField"=>"articleId"
        ]);
        $tableArticleClass = new tfdaoSingle($tfphp, [
            "name"=>"articleClass",
            "fields"=>[
                "classId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "className"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["classId"],
                "className"=>["className"]
            ],
            "autoIncrementField"=>"classId"
        ]);
        $tableArticleTag = new tfdaoSingle($tfphp, [
            "name"=>"articleTag",
            "fields"=>[
                "tagId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "tagName"=>["type"=>tfdao::FIELD_TYPE_STR],
            ],
            "constraints"=>[
                "default"=>["tagId"],
                "tagName"=>["tagName"]
            ],
            "autoIncrementField"=>"tagId"
        ]);
        $tableArticle_tag = new tfdaoSingle($tfphp, [
            "name"=>"article_tag",
            "fields"=>[
                "articleId"=>["type"=>tfdao::FIELD_TYPE_INT],
                "tagId"=>["type"=>tfdao::FIELD_TYPE_INT],
            ],
            "constraints"=>[
                "default"=>["articleId", "tagId"]
            ]
        ]);
        parent::__construct($tfphp, [
            "article"=>$tableArticle,
            "articleClass"=>$tableArticleClass,
            "articleTag"=>$tableArticleTag,
            "article_properties"=>new tfdaoOneToMany($tfphp, [
                $tableArticle,
                $tableArticleClass
            ], [
                "fieldMapping"=>[
                    [
                        "classId"=>"classId"
                    ]
                ]
            ]),
            "article_tags"=>new tfdaoManyToMany($tfphp, [
                $tableArticle,
                $tableArticleTag,
                $tableArticle_tag
            ], [
                "fieldMapping"=>[
                    [
                        "articleId"=>"articleId"
                    ], [
                        "tagId"=>"tagId"
                    ]
                ]
            ])
        ]);
    }
}