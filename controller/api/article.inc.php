<?php

namespace tfphp\controller\api;

use tfphp\framework\system\tfapi;
use tfphp\model\article as articleModel;

class article extends tfapi {
    protected function onLoad(){
        $article = new articleModel($this->tfphp);
        $articles = $article->getArticles();
        $this->responseJsonData($articles);
    }
}