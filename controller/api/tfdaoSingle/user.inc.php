<?php 

namespace tfphp\controller\api\tfdaoSingle;

use tfphp\framework\system\tfapi;
use tfphp\model\user as userModel;

class user extends tfapi{
    private function A(string $E, $A1){
        echo "<h3>". $E. "</h3>";
        echo "<pre>". json_encode($A1). "</pre>";
    }
    protected function onLoad(){
        $A6 = new userModel($this->tfphp);
        $AE = $A6->tfdaoSingleCRUD();
        foreach ($AE as $B1){
            $this->A($B1[0], $B1[1]);
        }
    }
}