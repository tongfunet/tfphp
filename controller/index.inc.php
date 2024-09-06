<?php

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\article;
use tfphp\model\user;

class index extends tfpage {
    protected function onLoad(){
        $user = new user($this->tfphp);
        $article = new article($this->tfphp);
        $users = $user->getUsers();
        $articles = $article->getArticles();
        $this->view->setVar("users", $users);
        $this->view->setVar("articles", $articles);
    }
}