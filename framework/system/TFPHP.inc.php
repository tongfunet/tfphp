<?php

namespace TFPHP\framework\system;

class TFPHP{
    private $class;
    public function __construct($class){
        $this->class = $class;
    }
    public function start(){
        $ru = $_SERVER["REQUEST_URI"];
        $p = strpos($ru, "?");
        if($p !== false){
            $pru = substr($ru, 0, $p);
            $qs = substr($ru, $p+1);
        }
        else{
            $pru = $ru;
            $qs = "";
        }
        if($pru == "/") $pru .= "index";
        $className = "TFPHP\\controller". str_replace("/", "\\", $pru);
        if(class_exists($className)){
            $class = new \ReflectionClass($className);
            if($class->getParentClass()->getName() == "TFPHP\\framework\\system\TFPage"){
                $classInstance = $class->newInstance();
                $classInstance->load();
            }
            else if($class->getParentClass()->getName() == "TFPHP\\framework\\system\TFAPI"){
                $classInstance = $class->newInstance();
                $classInstance->load();
            }
            else{
                throw new \Exception("class '". $className. "' is invalid");
            }
        }
        else{
            throw new \Exception("class '". $className. "' is not found");
        }
    }
    public static function run($class){
        (new TFPHP($class))->start();
    }
}