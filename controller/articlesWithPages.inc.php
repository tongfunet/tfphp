<?php 

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\article;

class articlesWithPages extends tfpage{
    protected function onLoad(){
        $A = new article($this->tfphp);
        $F = $A->getArticlesWithPages();
        $this->view->setVar("articles", $F);
    }
}