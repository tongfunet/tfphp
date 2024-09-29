<?php

namespace tfphp\controller\api;

use tfphp\framework\system\tfrestfulAPI;
use tfphp\model\article;
use tfphp\model\user;

class restfulAPI extends tfrestfulAPI {
    private function var_dump_test(string $title, $var){
        echo "<h3>". $title. "</h3>";
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
    private function postUser(){
        $user = new user($this->tfphp);
        try {
            $daoUser = $user->getDAOOneToOne("user");
            $ret = $daoUser->insert([
                "userName"=>"同福",
                "userPwd"=>md5("123456"),
                "createDT"=>date("Y-m-d H:i:s"),
                "nickName"=>"同福",
                "description"=>"这是同福的个人介绍"
            ]);
            $ret = $daoUser->insert([
                "userName"=>"tongfu",
                "userPwd"=>md5("123456"),
                "createDT"=>date("Y-m-d H:i:s"),
                "nickName"=>"tongfu",
                "description"=>"这是tongfu的个人介绍"
            ]);
            $ret = $daoUser->insert([
                "userName"=>"同福女生",
                "userPwd"=>md5("123456"),
                "createDT"=>date("Y-m-d H:i:s"),
                "nickName"=>"同福女生",
                "description"=>"这是同福女生的个人介绍"
            ]);
            $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
        }
        catch (\Exception $e){
            $this->responseJsonData(["errcode"=>1, "errmsg"=>$e->getMessage()]);
        }
    }
    private function postArticle(){
        $article = new article($this->tfphp);
        try{
            $daoArticle = $article->getDAOSingle("article");
            for($i=0;$i<10;$i++){
                $ret = $daoArticle->insert([
                    "title"=>"文章". date("Y-m-d H:i:s"),
                    "content"=>"文章". date("Y-m-d H:i:s"),
                    "createDT"=>date("Y-m-d H:i:s"),
                ]);
                sleep(1);
            }
            $this->responseJsonData(["errcode"=>0, "errmsg"=>"OK"]);
        }
        catch (\Exception $e){
            $this->responseJsonData(["errcode"=>1, "errmsg"=>$e->getMessage()]);
        }
    }
    private function testUser(){
        $user = new user($this->tfphp);
        try {
            $daoUser = $user->getDAOOneToOne("user");

            // insert
            $ret = $daoUser->insert([
                "userName"=>"鬼谷子叔叔",
                "userPwd"=>md5("123456"),
                "createDT"=>date("Y-m-d H:i:s"),
                "nickName"=>"鬼谷子叔叔",
                "description"=>"这是鬼谷子叔叔的个人介绍"
            ]);
            $this->var_dump_test("insert", $ret);

            // getLastInsert
            $userInfo = $daoUser->getLastInsert();
            $this->var_dump_test("getLastInsert", $userInfo);

            // select by auto-increment field
            $userInfo = $daoUser->select([
                $daoUser->getAutoIncrementField()=>$daoUser->getLastInsertAutoIncrementValue()
            ]);
            $this->var_dump_test("select by auto-increment field", $userInfo);

            // select by u_userName
            $userInfo = $daoUser->select([
                "userName"=>"鬼谷子叔叔"
            ], "u_userName");
            $this->var_dump_test("select by u_userName", $userInfo);

            // update
            $ret = $daoUser->update($userInfo, [
                "updateDT"=>date("Y-m-d H:i:s"),
                "nickName" => "鬼谷子叔叔！",
                "description"=>"这是鬼谷子叔叔的个人介绍！"
            ]);
            $this->var_dump_test("update", $ret);

            // select after update
            $userInfo = $daoUser->select($userInfo);
            $this->var_dump_test("select after update", $userInfo);

            // delete
            $ret = $daoUser->delete($userInfo);
            $this->var_dump_test("delete", $ret);
        }
        catch (\Exception $e){
            $this->var_dump_test("exception", $e->getMessage());
        }
    }
    private function testArticle(){
        $article = new article($this->tfphp);
        try {
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

            // insert article
            $ret = $daoArticle->insert([
                "title"=>"测试". date("Y-m-d H:i:s"),
                "content"=>"测试". date("Y-m-d H:i:s"),
                "createDT"=>date("Y-m-d H:i:s"),
            ]);
            $this->var_dump_test("insert article", $ret);

            // getLastInsert article
            $articleInfo = $daoArticle->getLastInsert();
            $this->var_dump_test("getLastInsert article", $articleInfo);

            // insert article class
            $ret = $daoArticleClass->insert([
                "className"=>"Technology"
            ]);
            $this->var_dump_test("insert article class", $ret);

            // getLastInsert article class
            $articleClassInfo = $daoArticleClass->getLastInsert();
            $this->var_dump_test("getLastInsert article class", $articleClassInfo);

            // update article properties
            $ret = $daoArticle_properties->update([
                $articleInfo,
                $articleClassInfo
            ]);
            $this->var_dump_test("update article properties", $ret);

            // select article after update
            $articleInfo = $daoArticle->select([
                "articleId"=>$articleInfo["articleId"]
            ]);
            $this->var_dump_test("select article after update", $articleInfo);

            // cycle insert article tag
            // cycle getLastInsert article tag
            foreach ($tags as $tag){
                $ret = $daoArticleTag->insert([
                    "tagName"=>$tag
                ]);
                $this->var_dump_test("cycle insert article tag", $ret);
                $articleTagInfo = $daoArticleTag->getLastInsert();
                $this->var_dump_test("cycle getLastInsert article tag", $articleTagInfo);
                $articleTagInfos[] = $articleTagInfo;
            }

            // bind article tags
            $ret = $daoArticle_tags->insertMultiple([
                $articleInfo,
                $articleTagInfos
            ]);
            $this->var_dump_test("bind article tags", $ret);

            // get article infos
            $results = $this->tfphp->getDataSource()->fetchAll("SELECT * 
                FROM article a 
                LEFT JOIN articleClass ac 
                ON a.classId = ac.classId", []);
            $this->var_dump_test("get article infos", $results);

            // get article tags
            $results = $this->tfphp->getDataSource()->fetchAll("SELECT * 
                FROM article a 
                INNER JOIN article_tag a_t 
                ON a.articleId = a_t.articleId 
                INNER JOIN articleTag at 
                ON a_t.tagId = at.tagId", []);
            $this->var_dump_test("get article tags", $results);

            // unbind article tags
            $daoArticle_tags->deleteMultiple([
                $articleInfo,
                $articleTagInfos
            ]);
            $this->var_dump_test("unbind article tags", $ret);

            // cycle delete article tag
            foreach ($articleTagInfos as $articleTagInfo){
                $ret = $daoArticleTag->delete($articleTagInfo, "default");
                $this->var_dump_test("cycle delete article tag", $ret);
            }

            // delete article class
            $ret = $daoArticleClass->delete($articleClassInfo, "default");
            $this->var_dump_test("delete article class", $ret);

            // delete article
            $ret = $daoArticle->delete($articleInfo, "default");
            $this->var_dump_test("delete article", $ret);
        }
        catch (\Exception $e){
            $this->var_dump_test("exception", $e->getMessage());
        }
    }
    protected function onLoad(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $function = $_SERVER["RESTFUL_RESOURCE_FUNCTION"];
            switch ($function){
                case "user":
                    $this->postUser();
                    break;
                case "article":
                    $this->postArticle();
                    break;
            }
        }
        else{
            $function = $_SERVER["RESTFUL_RESOURCE_FUNCTION"];
            switch ($function){
                case "user":
                    $this->testUser();
                    break;
                case "article":
                    $this->testArticle();
                    break;
            }
        }
    }
}