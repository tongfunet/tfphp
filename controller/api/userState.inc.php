<?php

namespace tfphp\controller\api;

use tfphp\framework\system\tfapi;

class userState extends tfapi {
    protected function onLoad(){
        $this->responsePlaintextData("this is a demo for API /userState");
    }
}