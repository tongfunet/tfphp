<?php 

namespace tfphp\controller;

use tfphp\framework\system\tfpage;
use tfphp\model\article;

class articles extends tfpage{
    protected function onLoad(){
        $A = new article($this->tfphp);
        $A4 = $A->getArticles();
        $this->view->setVar("articles", $A4);
    }
}