<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

declare(strict_types=1);

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
    public function __construct($obj){
        $this->obj = $obj;
        $this->request = new tfrequest($this);
        $this->response = new tfresponse($this);
        $this->config = $this->loadConfig();
        $this->dataSources = [];
        $this->redisObjs = [];
    }
    private function parseConfigXML(&$config){
        if(isset($config["@attributes"]) && is_array($config["@attributes"])){
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
    private function makeConfig(&$config){
        if(!isset($config["system"]) || !isset($config["system"]["serverUrl"])) $config["system"]["serverUrl"] = "";
    }
    private function loadConfig(): array{
        $configFileNamePath = TFPHP_DOCUMENT_ROOT. "/config/tfphp";
        if(file_exists($configFileNamePath. ".xml")){
            $config = simplexml_load_file($configFileNamePath. ".xml");
            $config = json_encode($config);
            $config = json_decode($config, true);
            $this->parseConfigXML($config);
            $this->makeConfig($config);
            return $config;
        }
        else if(file_exists($configFileNamePath. ".yaml")){
            $config = yaml_parse_file($configFileNamePath. ".yaml");
            $this->parseConfigYAML($config);
            $this->makeConfig($config);
            return $config;
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
    public function getDataSource(string $name=null): ?tfdo{
        if(!$name) $name = "default";
        if(!isset($this->config["database"][$name]) || !is_array($this->config["database"][$name])){
            return null;
        }
        if(!isset($this->dataSources[$name])) $this->dataSources[$name] = new tfdo($this, $this->config["database"][$name]);
        return $this->dataSources[$name];
    }
    public function getRedis(string $name=null): ?tfredis{
        if(!$name) $name = "default";
        if(!isset($this->config["redis"][$name]) || !is_array($this->config["redis"][$name])){
            return null;
        }
        if(!isset($this->redisObjs[$name])) $this->redisObjs[$name] = new tfredis($this, $this->config["redis"][$name]);
        return $this->redisObjs[$name];
    }
    public function serverURL(string $url): string{
        return $this->config["system"]["serverUrl"]. $url;
    }
    public function URL(string $url): string{
        return $this->serverURL($url);
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
                if(($p2 = strpos($qsItem, "=")) !== false) $_GET[substr($qsItem, 0, $p2)] = urldecode(substr($qsItem, $p2+1));
                else $_GET[$qsItem] = "";
            }
        }
        if(isset($_SERVER["HTTP_CONTENT_TYPE"]) && strpos($_SERVER["HTTP_CONTENT_TYPE"], "application/json") !== false){
            $_POST = json_decode(file_get_contents("php://input"), true);
            if(!is_array($_POST)) $_POST = [];
        }
        if(substr($pru, -1) == "/") $pru .= "index";
        $extension = "";
        $opru = $pru;
        if(($posExtension = strrpos($pru, ".")) !== false){
            $extension = strtolower(substr($pru, $posExtension+1));
            $pru = substr($pru, 0, $posExtension);
        }
        if(method_exists($this->obj, "getRERoutes")){
            $RERoutes = call_user_func([$this->obj, "getRERoutes"]);
            foreach ($RERoutes as $REURI => $mappingURI){
                $compiledREURI = preg_replace(["/([\/\-])/", "/\{[^\}]*\}/"], ["\\\\\\1", "([^\/]*)"], $REURI);
                if(preg_match("/^". $compiledREURI. "$/", $opru, $rg)){
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
        if(method_exists($this->obj, "getStaticRoutes")){
            $staticRoutes = call_user_func([$this->obj, "getStaticRoutes"]);
            foreach ($staticRoutes as $staticRoute => $mappingURI){
                if(strpos($pru, $staticRoute) === 0){
                    $opru = substr($opru, strlen($staticRoute));
                    $_SERVER["RESOURCE_NAME"] = TFPHP_DOCUMENT_ROOT. $mappingURI. $opru;
                    $_SERVER["RESOURCE_FILENAME"] = $mappingURI. $opru;
                    $pru = "/tfphp/_static";
                    break;
                }
            }
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
            throw new \Exception("classes '". implode("', '", $classNames). "' are not found", 660001);
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
        if(!$isRestfulMode){
            if(!preg_match("/\\\\(tfpage|tfapi|tfrestfulAPI)$/", $parentClassName)){
                throw new \Exception("class '". $className. "' is invalid", 660002);
            }
        }
        else{
            if(!preg_match("/\\\\(tfrestfulAPI)$/", $parentClassName)){
                throw new \Exception("class '" . $className . "' is invalid", 660003);
            }
            if($resName == "") $resName = basename($pru);
            $_SERVER["RESTFUL_RESOURCE_NAME"] = $resName;
            $_SERVER["RESTFUL_RESOURCE_VALUE"] = $resValue;
            $_SERVER["RESTFUL_RESOURCE_FUNCTION"] = $resFunction;
        }
        $classInstance = $class->newInstanceArgs([$this]);
        if(method_exists($this->obj, "getPluginsConfig")){
            $pluginsConfig = call_user_func([$this->obj, "getPluginsConfig"]);
            foreach ($pluginsConfig as $pluginName => $pluginConfig){
                if(isset($pluginConfig["className"])){
                    $classInstance->registerPlugin($pluginName,
                        $pluginConfig["className"],
                        (isset($pluginConfig["entryMethodName"])) ? $pluginConfig["entryMethodName"] : null);
                }
            }
        }
        $classInstance->load();
        return;
    }
    public static function run(string $class){
        (new tfphp((new \ReflectionClass($class))->newInstance()))->start();
    }
}
function tfdumpDebug(string $errno, string $errstr, string $errfile, int $errline, array $traces){
    $fc = file_get_contents($errfile);
    $lineNum = preg_match_all("/\n/", $fc, $rgs, PREG_OFFSET_CAPTURE);
    $posStart = ($errline-1 > 6) ? $rgs[0][$errline-1-6][1] : 0;
    $posLength = ($lineNum-$errline+1 > 5) ? $rgs[0][$errline-1+5][1] : filesize($errfile);
    $code = htmlspecialchars(substr($fc, $posStart, $posLength-$posStart));
    $traceStack = "";
    foreach ($traces as $trace) if(isset($trace["file"])) $traceStack .= sprintf("<span>%s</span><span style=\"color: #666;\"> in %s line %d</span><br />", ((isset($trace["class"])) ? $trace["class"]. "::". $trace["function"] : $trace["function"]). "()", $trace["file"], $trace["line"]);
    echo sprintf("<h1>%s (%d)</h1><span>in %s line %d</span><br /><pre style=\"border: 1px solid #ccc; padding: 12px;\">%s</pre><p>%s</p><div><a href=\"https://tongfu.net/tag/tfphp.html\">TFPHP</a> v0.6.9</div>", $errstr, $errno, $errfile, $errline, $code, $traceStack);
    exit(intval($errno));
}
function tfdumpError(string $errno, string $errstr, string $errfile, int $errline){
    throw new \ErrorException($errstr, 0, intval($errno), $errfile, $errline);
}
function tfdumpException($e){
    error_log(sprintf("[%s] %s in %s:%d\nStack trace:\n%s", date('Y-m-d H:i:s'), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));
    if(defined("TFPHP_DEBUG") && TFPHP_DEBUG) tfdumpDebug(strval($e->getCode()), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
}
set_error_handler("tfphp\\framework\\tfdumpError", E_ALL);
set_exception_handler("tfphp\\framework\\tfdumpException");
function tfphpAutoload($class){
    $className = str_replace("\\", "/", substr($class, 16));
    $classFilepath = TFPHP_ROOT. "/". $className. ".inc.php";
    if(!file_exists($classFilepath)){
        return false;
    }
    include_once $classFilepath;
    return true;
}
function tfprojectAutoload($class){
    $className = str_replace("\\", "/", substr($class, 6));
    $classFilepath = TFPHP_DOCUMENT_ROOT. "/". $className. ".inc.php";
    if(!file_exists($classFilepath)){
        return false;
    }
    include_once $classFilepath;
    return true;
}
spl_autoload_register("tfphp\\framework\\tfphpAutoload");
spl_autoload_register("tfphp\\framework\\tfprojectAutoload");
