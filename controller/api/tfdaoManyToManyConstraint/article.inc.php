<?php

namespace tfphp\controller\api\tfdaoManyToManyConstraint;

use tfphp\framework\system\tfapi;
use tfphp\model\article as articleModel;

class article extends tfapi {
    private function var_dump_test(string $title, $var){
        echo "<h3>". $title. "</h3>";
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
    protected function onLoad(){
        $articleModel = new articleModel($this->tfphp);
        $results = $articleModel->tfdaoManyToManyConstraintCRUD();
        foreach ($results as $result){
            $this->var_dump_test($result[0], $result[1]);
        }
    }
}