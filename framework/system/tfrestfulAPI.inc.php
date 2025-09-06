<?php 

namespace tfphp\framework\system;

use tfphp\framework\system\server\tfresponse;
use tfphp\framework\tfphp;

class tfrestfulAPI {
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
    protected function responseJSONData($A1, $A4=null){
        $this->tfphp->getResponse()->responseJSONData($A1, $A4);
    }
    protected function JSONData($A1, $A4=null){
        $this->responseJSONData($A1, $A4);
    }
    protected function JSON(int $AA, $A1, string $A4=null){
        $this->tfphp->getResponse()->JSON($AA, $A1, $A4);
    }
    protected function location(string $AC){
        $this->tfphp->getResponse()->location($AC);
    }
    protected function onLoad(){
        $B2 = strtoupper($_SERVER["REQUEST_METHOD"]);
        $B6 = $this->tfphp->getRequest()->getResourceFunction();
        if($B6 != "" && method_exists($this, "on_". $B6)) call_user_func_array([$this, "on_". $B6], []);
        else if($B6 != "" && method_exists($this, "on". $B2. "_". $B6)) call_user_func_array([$this, "on". $B2. "_". $B6], []);
        else if(method_exists($this, "on". $B2)) call_user_func_array([$this, "on". $B2], []);
        else $this->onLoadCustom();
    }
    protected function onLoadCustom(){

    }
    public function load(){
        $this->onLoad();
    }
}