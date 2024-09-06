<?php

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;
use tfphp\framework\model\tfdaoOneToMany;
use tfphp\framework\model\tfdaoManyToMany;

class article extends tfmodel{
    public function __construct(tfphp $tfphp){
        $tableArticle = new dao\article($tfphp);
        $tableArticleClass = new dao\articleClass($tfphp);
        $tableArticleTag = new dao\articleTag($tfphp);
        $tableArticle_tag = new dao\article_tag($tfphp);
        parent::__construct($tfphp, [
            "article"=>$tableArticle,
            "articleClass"=>$tableArticleClass,
            "articleTag"=>$tableArticleTag,
            "article_properties"=>new tfdaoOneToMany($tfphp, [
                $tableArticle,
                $tableArticleClass
            ], [
                "fieldMapping"=>[["classId"=>"classId"]]
            ]),
            "article_tags"=>new tfdaoManyToMany($tfphp, [
                $tableArticle,
                $tableArticleTag,
                $tableArticle_tag
            ], [
                "fieldMapping"=>[["articleId"=>"articleId"], ["tagId"=>"tagId"]]
            ])
        ]);
    }
    public function getArticles(): array{
        $ds = $this->tfphp->getDataSource();
        $articles = $ds->fetchAll3("select * from article a
                order by a.articleId desc", []);
        if($articles === null){
            return [];
        }
        return $articles;
    }
}