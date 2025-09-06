<?php 

namespace tfphp\controller\api\tfdaoManyToMany;

use tfphp\framework\system\tfapi;
use tfphp\model\article as articleModel;

class article extends tfapi{
    private function A(string $A0, $A3){
        echo "<h3>". $A0. "</h3>";
        echo "<pre>". json_encode($A3). "</pre>";
    }
    protected function onLoad(){
        $A8 = new articleModel($this->tfphp);
        $AF = $A8->tfdaoManyToManyCRUD();
        foreach ($AF as $B4){
            $this->A($B4[0], $B4[1]);
        }
    }
}