<?php

namespace tfphp;

use tfphp\framework\system\server\tfrequest;
use tfphp\framework\system\server\tfresponse;

define("TFPHP_ROOT", realpath(__DIR__. "/.."));
class tfphp{
    private tfrequest $request;
    private tfresponse $response;
    public function getRequest(): tfrequest{
        return $this->request;
    }
    public function getResponse(): tfresponse{
        return $this->response;
    }
    public function responseData(int $dataType, ?string $dataCharset, $data){
        $this->response->setDataType($dataType);
        $this->response->setDataCharset(($dataCharset) ? $dataCharset : "UTF-8");
        $this->response->setData($data);
        $this->response->response();
    }
    public function responseJsonData($data, string $dataCharset=null){
        $this->responseData(tfresponse::T_DATA_JSON, $dataCharset, $data);
    }
    public function responseHtmlData($data, string $dataCharset=null){
        $this->responseData(tfresponse::T_DATA_HTML, $dataCharset, $data);
    }
    public function responsePlaintextData($data, string $dataCharset=null){
        $this->responseData(tfresponse::T_DATA_PLAINTEXT, $dataCharset, $data);
    }
    public function start(){
        $this->request = new tfrequest($this);
        $this->response = new tfresponse($this);
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
        if($qs != ""){
            $qsArr = explode("&", $qs);
            foreach ($qsArr as $qsItem){
                if(($p2 = strpos($qsItem, "=")) !== false){
                    $_GET[substr($qsItem, 0, $p2)] = urldecode(substr($qsItem, $p2+1));
                }
                else{
                    $_GET[$qsItem] = "";
                }
            }
        }
        else{
            $ru = $pru = substr($ru, 0, -1);
        }
        if(substr($pru, -1) == "/") $pru .= "index";
        $extension = "";
        if(($p = strrpos($pru, ".")) !== false){
            $extension = strtolower(substr($pru, $p+1));
        }
        if($extension){
            $className = "tfphp\\controller\\tfphp\\_static";
            $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $className), 16). ".inc.php";
            $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
            $_SERVER["REQUEST_URI"] = $ru;
            $_SERVER["PHP_SELF"] = $pru;
            $_SERVER["DOCUMENT_ROOT"] = TFPHP_ROOT;
            $_SERVER["DOCUMENT_URI"] = TFPHP_ROOT. $_SERVER["PHP_SELF"];
            $class = new \ReflectionClass($className);
            $parentClassName = $class->getParentClass()->getName();
            $parentClassBaseName = substr($parentClassName, 23);
            if(!in_array($parentClassBaseName, ["tfapi"])){
                throw new \Exception("class '". $className. "' is invalid");
            }
            $classInstance = $class->newInstanceArgs([$this]);
            $classInstance->load();
            return;
        }
        $resName = $resValue = $resFunction = $resExtension = "";
        $className = "tfphp\\controller". str_replace("/", "\\", $pru);
        if(!class_exists($className)){
            if(substr(basename($pru), 0, 1) == "_"){
                $resFunction = substr(basename($pru), 1);
                $pru = dirname($pru);
            }
            $className = "tfphp\\controller". str_replace("/", "\\", $pru);
            $resName = basename($pru);
            if(!class_exists($className)){
                $resValue = basename($pru);
                $pru = dirname($pru);
                $resName = basename($pru);
                $className = "tfphp\\controller". str_replace("/", "\\", $pru);
            }
        }
        if(!class_exists($className)){
            throw new \Exception("class '". $className. "' is not found");
        }
        $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $className), 16). ".inc.php";
        $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
        $_SERVER["REQUEST_URI"] = $ru;
        $_SERVER["PHP_SELF"] = $pru;
        $_SERVER["DOCUMENT_ROOT"] = TFPHP_ROOT;
        $_SERVER["DOCUMENT_URI"] = TFPHP_ROOT. $_SERVER["PHP_SELF"];
        $class = new \ReflectionClass($className);
        $parentClassName = $class->getParentClass()->getName();
        $parentClassBaseName = substr($parentClassName, 23);
        if(!in_array($parentClassBaseName, ["tfpage", "tfapi", "tfrestfulAPI"])){
            throw new \Exception("class '". $className. "' is invalid");
        }
        if($parentClassBaseName == "tfrestfulAPI"){
            if($resName == ""){
                $resName = basename($pru);
            }
            $_SERVER["RESTFUL_RESOURCE_NAME"] = $resName;
            $_SERVER["RESTFUL_RESOURCE_VALUE"] = $resValue;
            $_SERVER["RESTFUL_RESOURCE_FUNCTION"] = $resFunction;
        }
        $classInstance = $class->newInstanceArgs([$this]);
        $classInstance->load();
    }
    public static function run(){
        (new tfphp())->start();
    }
}
function tfphpAutoload($class){
    $className = str_replace("\\", "/", substr($class, 6));
    $classFilepath = TFPHP_ROOT. "/". $className. ".inc.php";
    if(!file_exists($classFilepath)){
        return false;
    }
    include_once $classFilepath;
    return true;
}
spl_autoload_register("tfphp\\tfphpAutoload");
tfphp::run();