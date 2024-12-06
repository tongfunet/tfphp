<?php

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\article;

class articlesWithPages extends tfpage {
    protected function onLoad(){
        $article = new article($this->tfphp);
        $articles = $article->getArticlesWithPages();
        $this->view->setVar("articles", $articles);
    }
}