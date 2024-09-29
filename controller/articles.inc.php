<?php

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\article;

class articles extends tfpage {
    protected function onLoad(){
        $article = new article($this->tfphp);
        $articles = $article->getArticles();
        $this->view->setVar("articles", $articles);
    }
}