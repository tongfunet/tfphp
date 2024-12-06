<?php

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;

class article extends tfmodel{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp);
        $this->setDAOOneToMany("article_classes", ["article", "articleClass"], [["classId"=>"classId"]]);
        $this->setDAOManyToMany("article_tags", ["article", "articleTag", "article_tag"], [["articleId"=>"articleId"], ["tagId"=>"tagId"]]);
    }
    public function tfdaoManyToManyCRUD(): array{
        $ds = $this->tfphp->getDataSource();
        $article = $this->getSG("article");
        $articleClass = $this->getSG("articleClass");
        $articleTag = $this->getSG("articleTag");
        $article_tags = $this->getM2M("article_tags");
        $ds->execute3("truncate table article", []);
        $ds->execute3("truncate table articleClass", []);
        $ds->execute3("truncate table articleTag", []);
        $ds->execute3("truncate table article_tag", []);
        $results = [];
        $tagIds = [];
        $results[] = ["tfdaoManyToMany", "CRUD"];
        $results[] = ["insert tag php", $articleTag->insert([
            "tagName"=>"php"
        ])];
        $tagIds[] = $articleTag->getLastInsert()["tagId"];
        $results[] = ["insert tag java", $articleTag->insert([
            "tagName"=>"java"
        ])];
        $tagIds[] = $articleTag->getLastInsert()["tagId"];
        $results[] = ["insert tag python", $articleTag->insert([
            "tagName"=>"python"
        ])];
        $tagIds[] = $articleTag->getLastInsert()["tagId"];
        $results[] = ["insert single tag article", $article->insert([
            "title"=>"单标签文章". date("YmdHis"),
            "content"=>"正文". date("YmdHis"),
            "createDT"=>date("Y-m-d H:i:s")
        ])];
        $singleTagArticleId = $article->getLastInsert()["articleId"];
        $results[] = ["select single tag article", $article->select(["articleId"=>$singleTagArticleId])];
        $randomTagIndex = rand(0, count($tagIds)-1);
        $results[] = ["set single tag", $article_tags->insert(
            ["articleId"=>$singleTagArticleId],
            ["tagId"=>$tagIds[$randomTagIndex]]
        )];
        $results[] = ["insert multiple tags article", $article->insert([
            "title"=>"多标签文章". date("YmdHis"),
            "content"=>"正文". date("YmdHis"),
            "createDT"=>date("Y-m-d H:i:s")
        ])];
        $multipleTagArticleId = $article->getLastInsert()["articleId"];
        $results[] = ["select multiple tags article", $article->select(["articleId"=>$multipleTagArticleId])];
        $results[] = ["set multiple tags", $article_tags->insertMultiple(
            ["articleId"=>$multipleTagArticleId],
            [
                ["tagId"=>1],
                ["tagId"=>2],
                ["tagId"=>3]
            ]
        )];
        $articleInfos = $article->selectAll([]);
        foreach ($articleInfos as $k => $articleInfo){
            $articleInfos[$k]["tags"] = $article_tags->select(["articleId"=>$articleInfo["articleId"]]);
        }
        $results[] = ["select articles", $articleInfos];
        $results[] = ["unset single tag", $article_tags->delete(
            ["articleId"=>$singleTagArticleId],
            ["tagId"=>$tagIds[$randomTagIndex]]
        )];
        $results[] = ["unset multiple tags", $article_tags->deleteMultiple(
            ["articleId"=>$multipleTagArticleId],
            [
                ["tagId"=>1],
                ["tagId"=>2],
                ["tagId"=>3]
            ]
        )];
        $articleInfos = $article->selectAll([]);
        foreach ($articleInfos as $k => $articleInfo){
            $articleInfos[$k]["tags"] = $article_tags->select(["articleId"=>$articleInfo["articleId"]]);
        }
        $results[] = ["select articles", $articleInfos];
        return $results;
    }
    public function tfdaoManyToManyConstraintCRUD(): array{
        $ds = $this->tfphp->getDataSource();
        $article = $this->getSG("article");
        $articleClass = $this->getSG("articleClass");
        $articleTag = $this->getSG("articleTag");
        $article_tags = $this->getM2M("article_tags");
        $ds->execute3("truncate table article", []);
        $ds->execute3("truncate table articleClass", []);
        $ds->execute3("truncate table articleTag", []);
        $ds->execute3("truncate table article_tag", []);
        $results = [];
        $tagIds = [];
        $results[] = ["tfdaoManyToManyConstraint", "CRUD"];
        $results[] = ["insert tag php", $articleTag->insert([
            "tagName"=>"php"
        ])];
        $tagIds[] = $articleTag->getLastInsert()["tagId"];
        $results[] = ["insert tag java", $articleTag->insert([
            "tagName"=>"java"
        ])];
        $tagIds[] = $articleTag->getLastInsert()["tagId"];
        $results[] = ["insert tag python", $articleTag->insert([
            "tagName"=>"python"
        ])];
        $tagIds[] = $articleTag->getLastInsert()["tagId"];
        $results[] = ["insert single tag article", $article->insert([
            "title"=>"单标签文章". date("YmdHis"),
            "content"=>"正文". date("YmdHis"),
            "createDT"=>date("Y-m-d H:i:s")
        ])];
        $singleTagArticleId = $article->getLastInsert()["articleId"];
        $results[] = ["select single tag article", $article->constraintSelect([$singleTagArticleId])];
        $randomTagIndex = rand(0, count($tagIds)-1);
        $results[] = ["set single tag", $article_tags->insert(
            ["articleId"=>$singleTagArticleId],
            ["tagId"=>$tagIds[$randomTagIndex]]
        )];
        $results[] = ["insert multiple tags article", $article->insert([
            "title"=>"多标签文章". date("YmdHis"),
            "content"=>"正文". date("YmdHis"),
            "createDT"=>date("Y-m-d H:i:s")
        ])];
        $multipleTagArticleId = $article->getLastInsert()["articleId"];
        $results[] = ["select multiple tags article", $article->constraintSelect([$multipleTagArticleId])];
        $results[] = ["set multiple tags", $article_tags->insertMultiple(
            ["articleId"=>$multipleTagArticleId],
            [
                ["tagId"=>1],
                ["tagId"=>2],
                ["tagId"=>3]
            ]
        )];
        $articleInfos = $article->selectAll([]);
        foreach ($articleInfos as $k => $articleInfo){
            $articleInfos[$k]["tags"] = $article_tags->constraintSelect([$articleInfo["articleId"]]);
        }
        $results[] = ["select articles", $articleInfos];
        $results[] = ["unset single tag", $article_tags->delete(
            ["articleId"=>$singleTagArticleId],
            ["tagId"=>$tagIds[$randomTagIndex]]
        )];
        $results[] = ["unset multiple tags", $article_tags->deleteMultiple(
            ["articleId"=>$multipleTagArticleId],
            [
                ["tagId"=>1],
                ["tagId"=>2],
                ["tagId"=>3]
            ]
        )];
        $articleInfos = $article->selectAll([]);
        foreach ($articleInfos as $k => $articleInfo){
            $articleInfos[$k]["tags"] = $article_tags->constraintSelect([$articleInfo["articleId"]]);
        }
        $results[] = ["select articles", $articleInfos];
        return $results;
    }
}