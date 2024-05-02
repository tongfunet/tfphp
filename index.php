<?php

use TFPHP\framework\system\TFPHP;

class index{
    public function __construct(){
        TFPHP::run($this);
    }
}

(new index());

function __autoload(string $class){$classFilepath = __DIR__. "/". str_replace("\\", "/", substr($class, 6)). ".inc.php";if(file_exists($classFilepath)){require_once $classFilepath;return true;}return false;}