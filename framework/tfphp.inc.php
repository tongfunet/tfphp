<?php

namespace tfphp\framework;

use tfphp\framework\database\tfdo;
use tfphp\framework\database\tfredis;
use tfphp\framework\system\server\tfrequest;
use tfphp\framework\system\server\tfresponse;

define("TFPHP_ROOT", __DIR__);
class tfphp{
    private $obj;
    private tfrequest $request;
    private tfresponse $response;
    private array $config;
    private array $dataSources;
    private array $redisObjs;
    public function __construct($A){
        $this->obj = $A;
        $this->request = new tfrequest($this);
        $this->response = new tfresponse($this);
        $this->config = $this->loadConfig();
        $this->dataSources = [];
        $this->redisObjs = [];
    }
    private function parseConfigXML(&$A7){
        if(isset($A7["@attributes"])){
            $A7 = array_merge($A7, $A7["@attributes"]);
            unset($A7["@attributes"]);
        }
        if(is_array($A7)){
            foreach ($A7 as $B6 => $v){
                $this->parseConfigXML($A7[$B6]);
            }
        }
    }
    private function parseConfigYAML(&$A7){

    }
    private function loadConfig(): array{
        $B8 = TFPHP_DOCUMENT_ROOT. "/config/tfphp";
        if(file_exists($B8. ".xml")){
            $A7 = simplexml_load_file($B8. ".xml");
            $A7 = json_encode($A7) ;
            $A7 = json_decode($A7, true) ;
            $this->parseConfigXML($A7);
            return $A7;
        }
        else if(file_exists($B8. ".yaml")){
            $A7 = yaml_parse_file($B8. ".yaml");
            $this->parseConfigYAML($A7);
            return $A7;
        }
        return [];
    }
    public function getRequest(): tfrequest{
        return $this->request;
    }
    public function getResponse(): tfresponse{
        return $this->response;
    }
    public function getConfig(): array{
        return $this->config;
    }
    public function getDataSource(string $BA=null): ?tfdo{
        if(!$BA) $BA = "default";
        if(!isset($this->config["database"][$BA])){
            return null;
        }
        if(!isset($this->dataSources[$BA])) $this->dataSources[$BA] = new tfdo($this, $this->config["database"][$BA]);
        return $this->dataSources[$BA];
    }
    public function getRedis(string $BA=null): ?tfredis{
        if(!$BA) $BA = "default";
        if(!isset($this->config["redis"][$BA])){
            return null;
        }
        if(!isset($this->redisObjs[$BA])) $this->redisObjs[$BA] = new tfredis($this, $this->config["redis"][$BA]);
        return $this->redisObjs[$BA];
    }
    public function start(){
        $BF = $_SERVER["REQUEST_URI"];
        $C0 = strpos($BF, "?") ;
        if($C0 !== false){
            $C2 = substr($BF, 0, $C0);
            $C3 = substr($BF, $C0+1) ;
        }
        else{
            $C2 = $BF;
            $C3 = "" ;
        }
        if($C3 != ""){
            $C5 = explode("&", $C3);
            foreach ($C5 as $qsItem){
                if(($C6 = strpos($qsItem, "=")) !== false) $_GET[substr($qsItem, 0, $C6)] = urldecode(substr($qsItem, $C6+1));
                else $_GET[$qsItem] = "";
            }
        }
        if(strpos($_SERVER["HTTP_CONTENT_TYPE"], "application/json") !== false) $C7 = json_decode(file_get_contents("php://input"), true);
        if(substr($C2, -1) == "/") $C2 .= "index";
        $C9 = "";
        if(($CD = strrpos($C2, ".")) !== false){
            $C9 = strtolower(substr($C2, $CD+1));
            $D1 = $C2 ;
            $C2 = substr($C2, 0, $CD) ;
        }
        if(method_exists($this->obj, "getRERoutes")){
            $D5 = call_user_func([$this->obj, "getRERoutes"]);
            foreach ($D5 as $D8 => $mappingURI){
                $DA = preg_replace(["/([\/\-])/", "/\{[^\}]*\}/"], ["\\\\\\1", "([^\/]*)"], $D8);
                if(preg_match("/^". $DA. "$/", $D1, $rg)){
                    preg_match_all("/\{([^\}]*)\}/", $D8, $rgs);
                    $_SERVER["PATH_ARGV"] = [];
                    for($DE=1;$DE<count($rg);$DE++){
                        $_SERVER["PATH_ARGV"][$rgs[1][$DE-1]] = urldecode($rg[$DE]);
                    }
                    $C2 = $mappingURI;
                    break;
                }
            }
        }
        if(method_exists($this->obj, "getStaticRoutes")){
            $E3 = call_user_func([$this->obj, "getStaticRoutes"]);
            foreach ($E3 as $E4 => $mappingURI){
                if(strpos($C2, $E4) === 0){
                    $D1 = substr($D1, strlen($E4));
                    $_SERVER["RESOURCE_NAME"] = TFPHP_DOCUMENT_ROOT. $mappingURI. $D1;
                    $_SERVER["RESOURCE_FILENAME"] = $mappingURI. $D1;
                    $C2 = "/tfphp/_static" ;
                    break;
                }
            }
        }
        if(substr($C2, 0, 7) == "/tfphp/"){
            $E8 = "tfphp\\framework\\controller\\tfphp\\". substr($C2, 7);
            if(!class_exists($E8)){
                throw new \Exception("class '". $E8. "' is not found");
            }
            $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $E8), 19). ".inc.php";
            $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
            $_SERVER["REQUEST_URI"] = $BF;
            $_SERVER["PHP_SELF"] = $C2;
            $_SERVER["DOCUMENT_ROOT"] = TFPHP_DOCUMENT_ROOT;
            $_SERVER["DOCUMENT_URI"] = TFPHP_DOCUMENT_ROOT. $_SERVER["PHP_SELF"];
            $_SERVER["RESOURCE_EXTENSION"] = $C9;
            $EC = new \ReflectionClass($E8) ;
            $EF = $EC->getParentClass()->getName() ;
            $F3 = substr($EF, 23) ;
            if(!in_array($F3, ["tfpage", "tfapi", "tfrestfulAPI"])){
                throw new \Exception("class '". $E8. "' is invalid");
            }
            $F5 = $EC->newInstanceArgs([$this]) ;
            $F5->load();
            return;
        }
        $F6 = [] ;
        $F9 = $FD = $A01 = $A04 = "" ;
        $A05 = false ;
        $A07 = false ;
        if(!$A05){
            $E8 = "tfphp\\controller". str_replace("/", "\\", $C2);
            $F6[] = $E8;
            if(class_exists($E8)){
                $A05 = true;
            }
        }
        if(!$A05 && basename($C2)[0] == '_'){
            $A01 = substr(basename($C2), 1);
            $C2 = dirname($C2) ;
            $E8 = "tfphp\\controller". str_replace("/", "\\", $C2) ;
            $F6[] = $E8;
            if(class_exists($E8)){
                $A05 = true;
                $A07 = true ;
            }
        }
        if(!$A05){
            $FD = basename($C2);
            $C2 = dirname($C2) ;
            $F9 = basename($C2) ;
            $E8 = "tfphp\\controller". str_replace("/", "\\", $C2) ;
            $F6[] = $E8;
            if(class_exists($E8)){
                $A05 = true;
                $A07 = true ;
            }
        }
        if(!$A05){
            throw new \Exception("class '". implode("', '", $F6). "' are not found");
        }
        $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $E8), 16). ".inc.php";
        $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
        $_SERVER["REQUEST_URI"] = $BF;
        $_SERVER["PHP_SELF"] = $C2;
        $_SERVER["DOCUMENT_ROOT"] = TFPHP_DOCUMENT_ROOT;
        $_SERVER["DOCUMENT_URI"] = TFPHP_DOCUMENT_ROOT. $_SERVER["PHP_SELF"];
        $_SERVER["RESOURCE_EXTENSION"] = $C9;
        $EC = new \ReflectionClass($E8) ;
        $EF = $EC->getParentClass()->getName() ;
        $F3 = substr($EF, 23) ;
        if(!$A07){
            if(!in_array($F3, ["tfpage", "tfapi", "tfrestfulAPI"])){
                throw new \Exception("class '". $E8. "' is invalid");
            }
        }
        else{
            if(!in_array($F3, ["tfrestfulAPI"])){
                throw new \Exception("class '". $E8. "' is invalid");
            }
            if($F9 == "") $F9 = basename($C2);
            $_SERVER["RESTFUL_RESOURCE_NAME"] = $F9;
            $_SERVER["RESTFUL_RESOURCE_VALUE"] = $FD;
            $_SERVER["RESTFUL_RESOURCE_FUNCTION"] = $A01;
        }
        $F5 = $EC->newInstanceArgs([$this]) ;
        $F5->load();
        return;
    }
    public static function run(string $EC){
        (new tfphp((new \ReflectionClass($EC))->newInstance()))->start();
    }
}
function tfphpAutoload($EC){
    $E8 = str_replace("\\", "/", substr($EC, 16));
    $A0D = TFPHP_ROOT. "/". $E8. ".inc.php" ;
    if(!file_exists($A0D)){
        return false;
    }
    include_once $A0D;
    return true;
}
function tfprojectAutoload($EC){
    $E8 = str_replace("\\", "/", substr($EC, 6));
    $A0D = TFPHP_DOCUMENT_ROOT. "/". $E8. ".inc.php" ;
    if(!file_exists($A0D)){
        return false;
    }
    include_once $A0D;
    return true;
}
spl_autoload_register("tfphp\\framework\\tfphpAutoload");
spl_autoload_register("tfphp\\framework\\tfprojectAutoload");