<?php

namespace tfphp\framework;

use tfphp\framework\database\tfdo;
use tfphp\framework\system\server\tfrequest;
use tfphp\framework\system\server\tfresponse;

define("TFPHP_ROOT", realpath(__DIR__. "/.."));
class tfphp{
    private tfrequest $request;
    private tfresponse $response;
    private array $config;
    private array $dataSources;
    public function __construct(){
        $this->request = new tfrequest($this);
        $this->response = new tfresponse($this);
        $this->config = $this->loadConfig();
        $this->dataSources = [];
    }
    public function getRequest(): tfrequest{
        return $this->request;
    }
    public function getResponse(): tfresponse{
        return $this->response;
    }
    private function parseConfigXML(&$config){
        if(isset($config["@attributes"])){
            $config = array_merge($config, $config["@attributes"]);
            unset($config["@attributes"]);
        }
        if(is_array($config)){
            foreach ($config as $k => $v){
                $config[$k] = $this->parseConfigXML($config[$k]);
            }
        }
        return $config;
    }
    private function loadConfig(): array{
        $configFilepath = TFPHP_ROOT. "/config/tfphp.xml";
        if(file_exists($configFilepath)){
            $config = simplexml_load_file(TFPHP_ROOT. "/config/tfphp.xml");
            $config = json_encode($config);
            $config = json_decode($config, true);
            $config = $this->parseConfigXML($config);
            return $config;
        }
        return [];
    }
    public function getConfig(): array{
        return $this->config;
    }
    public function getDataSource(string $name=null): tfdo{
        if(!$name) $name = "default";
        if(!isset($this->dataSources[$name])){
            $this->dataSources[$name] = new tfdo($this, $this->config["database"][$name]);
        }
        return $this->dataSources[$name];
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
spl_autoload_register("tfphp\\framework\\tfphpAutoload");
tfphp::run();