<?php

namespace tfphp\controller\api\tfdaoSingleConstraint;

use tfphp\framework\system\tfapi;
use tfphp\model\user as userModel;

class user extends tfapi {
    private function var_dump_test(string $title, $var){
        echo "<h3>". $title. "</h3>";
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
    protected function onLoad(){
        $user = new userModel($this->tfphp);
        $results = $user->tfdaoSingleConstraintCRUD();
        foreach ($results as $result){
            $this->var_dump_test($result[0], $result[1]);
        }
    }
}