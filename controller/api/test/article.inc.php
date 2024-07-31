<?php

namespace tfphp\controller\api\test;

use tfphp\framework\system\tfapi;
use tfphp\model\article as modelArticle;

class article extends tfapi {
    protected function onLoad(){
        $article = new modelArticle($this->tfphp);
        try {
            $this->tfphp->responsePlaintextData("");
            $daoArticle = $article->getDAOSingle("article");
            $daoArticleClass = $article->getDAOSingle("articleClass");
            $daoArticleTag = $article->getDAOSingle("articleTag");
            $daoArticle_properties = $article->getDAOOneToMany("article_properties");
            $daoArticle_tags = $article->getDAOManyToMany("article_tags");
            $articleInfo = null;
            $articleClassInfo = null;
            $articleTagInfos = [];
            $tags = [
                "PHP", "Python", "Golang"
            ];

            $ret = $daoArticle->insert([
                "title"=>"测试". date("Y-m-d H:i:s")
            ]);
            var_dump("insert article", $ret);

            $articleInfo = $daoArticle->getLastInsert();
            var_dump("getLastInsert article", $articleInfo);

            $ret = $daoArticleClass->insert([
                "className"=>"Technology"
            ]);
            var_dump("insert article class", $ret);

            $articleClassInfo = $daoArticleClass->getLastInsert();
            var_dump("getLastInsert article class", $articleClassInfo);

            $ret = $daoArticle_properties->update([
                $articleInfo,
                $articleClassInfo
            ]);
            var_dump("update article properties", $ret);

            $articleInfo = $daoArticle->select($articleInfo, "default");
            var_dump("select after update", $articleInfo);

            foreach ($tags as $tag){
                $ret = $daoArticleTag->insert([
                    "tagName"=>$tag
                ]);
                var_dump("insert article tag", $ret);
                $articleTagInfo = $daoArticleTag->getLastInsert();
                var_dump("getLastInsert article tag", $articleTagInfo);
                $articleTagInfos[] = $articleTagInfo;
            }

            $ret = $daoArticle_tags->insertMultiple([
                $articleInfo,
                $articleTagInfos
            ]);
            var_dump("insert article tags", $ret);

            $results = $this->tfphp->getDataSource()->fetchAll("SELECT * 
                FROM article a 
                LEFT JOIN articleClass ac ON a.classId = ac.classId", []);
            var_dump("article infos", $results);

            $results = $this->tfphp->getDataSource()->fetchAll("SELECT * 
                FROM article a 
                INNER JOIN article_tag a_t ON a.articleId = a_t.articleId 
                INNER JOIN articleTag at ON a_t.tagId = at.tagId", []);
            var_dump("article tags", $results);

            $daoArticle_tags->deleteMultiple([
                $articleInfo,
                $articleTagInfos
            ]);
            var_dump("delete article tags", $ret);

            foreach ($articleTagInfos as $articleTagInfo){
                $ret = $daoArticleTag->delete($articleTagInfo, "default");
                var_dump("delete article tag", $ret);
            }

            $ret = $daoArticleClass->delete($articleClassInfo, "default");
            var_dump("delete article class", $ret);

            $ret = $daoArticle->delete($articleInfo, "default");
            var_dump("delete article", $ret);
        }
        catch (\Exception $e){
            var_dump($e->getMessage());
        }
    }
}