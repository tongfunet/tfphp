<?php

namespace tfphp\framework;

use tfphp\framework\database\tfdo;
use tfphp\framework\system\server\tfrequest;
use tfphp\framework\system\server\tfresponse;

define("TFPHP_ROOT", realpath(__DIR__. "/.."));
define("TFPHP_DOCUMENT_ROOT", realpath(__DIR__. "/.."));
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
                $this->parseConfigXML($config[$k]);
            }
        }
    }
    private function parseConfigYAML(&$config){

    }
    private function loadConfig(): array{
        $configFileNamePath = TFPHP_DOCUMENT_ROOT. "/config/tfphp";
        if(file_exists($configFileNamePath. ".xml")){
            $config = simplexml_load_file($configFileNamePath. ".xml");
            $config = json_encode($config);
            $config = json_decode($config, true);
            $this->parseConfigXML($config);
            return $config;
        }
        else if(file_exists($configFileNamePath. ".yaml")){
            $config = yaml_parse_file($configFileNamePath. ".yaml");
            $this->parseConfigYAML($config);
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
    public function responseJsonData($data, string $dataCharset=null, bool $stopScript=true){
        $this->responseData(tfresponse::T_DATA_JSON, $dataCharset, $data);
        if($stopScript) die;
    }
    public function responseHtmlData($data, string $dataCharset=null, bool $stopScript=true){
        $this->responseData(tfresponse::T_DATA_HTML, $dataCharset, $data);
        if($stopScript) die;
    }
    public function responsePlaintextData($data, string $dataCharset=null, bool $stopScript=true){
        $this->responseData(tfresponse::T_DATA_PLAINTEXT, $dataCharset, $data);
        if($stopScript) die;
    }
    public function location(string $url, bool $stopScript=true){
        header("Location: ". $url);
        if($stopScript) die;
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
        if(strpos($_SERVER["HTTP_CONTENT_TYPE"], "application/json") !== false){
            $_POST = json_decode(file_get_contents("php://input"), true);
        }
        if(substr($pru, -1) == "/") $pru .= "index";
        if(method_exists($this, "getRERoutes")){
            $RERoutes = call_user_func([$this, "getRERoutes"]);
            foreach ($RERoutes as $REURI => $mappingURI){
                $compiledREURI = preg_replace(["/([\/\-])/", "/\{[^\}]*\}/"], ["\\\\\\1", "([^\/]*)"], $REURI);
                if(preg_match("/^". $compiledREURI. "$/", $pru, $rg)){
                    preg_match_all("/\{([^\}]*)\}/", $REURI, $rgs);
                    $_SERVER["PATH_ARGV"] = [];
                    for($i=1;$i<count($rg);$i++){
                        $_SERVER["PATH_ARGV"][$rgs[1][$i-1]] = urldecode($rg[$i]);
                    }
                    $pru = $mappingURI;
                    break;
                }
            }
        }
        $extension = "";
        if(($p = strrpos($pru, ".")) !== false){
            $extension = strtolower(substr($pru, $p+1));
            $pru = substr($pru, 0, $p);
        }
        if(method_exists($this, "getStaticRoutes")){
            $staticRoutes = call_user_func([$this, "getStaticRoutes"]);
            foreach ($staticRoutes as $staticRoute => $mappingURI){
                if(strpos($pru, $staticRoute) === 0){
                    $pru = substr($pru, strlen($staticRoute));
                    if($extension) $pru .= ".". $extension;
                    $_SERVER["RESOURCE_NAME"] = TFPHP_DOCUMENT_ROOT. $mappingURI. $pru;
                    $_SERVER["RESOURCE_FILENAME"] = $mappingURI. $pru;
                    $pru = "/tfphp/_static";
                    break;
                }
            }
        }
        if(substr($pru, 0, 7) == "/tfphp/"){
            $className = "tfphp\\controller\\tfphp\\". substr($pru, 7);
            if(!class_exists($className)){
                throw new \Exception("class '". $className. "' is not found");
            }
            $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $className), 19). ".inc.php";
            $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
            $_SERVER["REQUEST_URI"] = $ru;
            $_SERVER["PHP_SELF"] = $pru;
            $_SERVER["DOCUMENT_ROOT"] = TFPHP_DOCUMENT_ROOT;
            $_SERVER["DOCUMENT_URI"] = TFPHP_DOCUMENT_ROOT. $_SERVER["PHP_SELF"];
            $_SERVER["RESOURCE_EXTENSION"] = $extension;
            $class = new \ReflectionClass($className);
            $parentClassName = $class->getParentClass()->getName();
            $parentClassBaseName = substr($parentClassName, 23);
            if(!in_array($parentClassBaseName, ["tfpage", "tfapi", "tfrestfulAPI"])){
                throw new \Exception("class '". $className. "' is invalid");
            }
            $classInstance = $class->newInstanceArgs([$this]);
            $classInstance->load();
            return;
        }
        $classNames = [];
        $resName = $resValue = $resFunction = $resExtension = "";
        $classIsFound = false;
        $isRestfulMode = false;
        if(!$classIsFound){
            $className = "tfphp\\controller". str_replace("/", "\\", $pru);
            $classNames[] = $className;
            if(class_exists($className)){
                $classIsFound = true;
            }
        }
        if(!$classIsFound && basename($pru)[0] == '_'){
            $resFunction = substr(basename($pru), 1);
            $pru = dirname($pru);
            $className = "tfphp\\controller". str_replace("/", "\\", $pru);
            $classNames[] = $className;
            if(class_exists($className)){
                $classIsFound = true;
                $isRestfulMode = true;
            }
        }
        if(!$classIsFound){
            $resValue = basename($pru);
            $pru = dirname($pru);
            $resName = basename($pru);
            $className = "tfphp\\controller". str_replace("/", "\\", $pru);
            $classNames[] = $className;
            if(class_exists($className)){
                $classIsFound = true;
                $isRestfulMode = true;
            }
        }
        if(!$classIsFound){
            throw new \Exception("class '". implode("', '", $classNames). "' are not found");
        }
        $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $className), 16). ".inc.php";
        $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
        $_SERVER["REQUEST_URI"] = $ru;
        $_SERVER["PHP_SELF"] = $pru;
        $_SERVER["DOCUMENT_ROOT"] = TFPHP_DOCUMENT_ROOT;
        $_SERVER["DOCUMENT_URI"] = TFPHP_DOCUMENT_ROOT. $_SERVER["PHP_SELF"];
        $_SERVER["RESOURCE_EXTENSION"] = $extension;
        $class = new \ReflectionClass($className);
        $parentClassName = $class->getParentClass()->getName();
        $parentClassBaseName = substr($parentClassName, 23);
        if(!$isRestfulMode){
            if(!in_array($parentClassBaseName, ["tfpage", "tfapi", "tfrestfulAPI"])){
                throw new \Exception("class '". $className. "' is invalid");
            }
        }
        else{
            if(!in_array($parentClassBaseName, ["tfrestfulAPI"])){
                throw new \Exception("class '". $className. "' is invalid");
            }
            if($resName == ""){
                $resName = basename($pru);
            }
            $_SERVER["RESTFUL_RESOURCE_NAME"] = $resName;
            $_SERVER["RESTFUL_RESOURCE_VALUE"] = $resValue;
            $_SERVER["RESTFUL_RESOURCE_FUNCTION"] = $resFunction;
        }
        $classInstance = $class->newInstanceArgs([$this]);
        $classInstance->load();
        return;
    }
    public function getStaticRoutes(): array{
        return [
            "/css/"=>"/css/",
            "/js/"=>"/js/"
        ];
    }
    public function getRERoutes(): array{
        return [
            "/class/{class}/doc/{doc}.html"=>"/classDoc",
        ];
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
function tfprojectAutoload($class){
    $className = str_replace("\\", "/", substr($class, 10));
    $classFilepath = TFPHP_DOCUMENT_ROOT. "/". $className. ".inc.php";
    if(!file_exists($classFilepath)){
        return false;
    }
    include_once $classFilepath;
    return true;
}
spl_autoload_register("tfphp\\framework\\tfphpAutoload");
spl_autoload_register("tfphp\\framework\\tfprojectAutoload");
tfphp::run();