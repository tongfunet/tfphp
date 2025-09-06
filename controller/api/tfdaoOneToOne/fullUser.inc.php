<?php 

namespace tfphp\controller\api\tfdaoOneToOne;

use tfphp\framework\system\tfapi;
use tfphp\model\user as userModel;

class fullUser extends tfapi{
    private function A(string $F, $A0){
        echo "<h3>". $F. "</h3>";
        echo "<pre>". json_encode($A0). "</pre>";
    }
    protected function onLoad(){
        $A4 = new userModel($this->tfphp);
        $A9 = $A4->tfdaoOneToOneCRUD();
        foreach ($A9 as $AA){
            $this->A($AA[0], $AA[1]);
        }
    }
}