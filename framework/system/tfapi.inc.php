<?php 

namespace tfphp\framework\system;

use tfphp\framework\system\server\tfresponse;
use tfphp\framework\tfphp;

class tfapi {
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
    protected function responseJSONData($A1, $A7=null){
        $this->tfphp->getResponse()->responseJSONData($A1, $A7);
    }
    protected function JSONData($A1, $A7=null){
        $this->responseJSONData($A1, $A7);
    }
    protected function JSON(int $AA, $A1, string $A7=null){
        $this->tfphp->getResponse()->JSON($AA, $A1, $A7);
    }
    protected function responseHTMLData($A1, string $A7=null){
        $this->tfphp->getResponse()->responseHTMLData($A1, $A7);
    }
    protected function HTMLData($A1, string $A7=null){
        $this->responseHTMLData($A1, $A7);
    }
    protected function HTML(int $AA, $A1, string $A7=null){
        $this->tfphp->getResponse()->HTML($AA, $A1, $A7);
    }
    protected function responsePlainTextData($A1, string $A7=null){
        $this->tfphp->getResponse()->responsePlainTextData($A1, $A7);
    }
    protected function PlainTextData($A1, string $A7=null){
        $this->responsePlainTextData($A1, $A7);
    }
    protected function PlainText(int $AA, $A1, string $A7=null){
        $this->tfphp->getResponse()->PlainText($AA, $A1, $A7);
    }
    protected function location(string $AD){
        $this->tfphp->getResponse()->location($AD);
    }
    protected function onLoad(){

    }
    public function load(){
        $this->onLoad();
    }
}