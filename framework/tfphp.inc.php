<?php 

declare(strict_types=1);

namespace tfphp\framework;

use tfphp\framework\database\tfdo;
use tfphp\framework\database\tfredis;
use tfphp\framework\system\server\tfrequest;
use tfphp\framework\system\server\tfresponse;

define("TFPHP_ROOT", __DIR__);
class tfphp{
    private $A;
    private tfrequest $C;
    private tfresponse $E;
    private array $A3;
    private array $A4;
    private array $A8;
    public function __construct($AC){
        $this->A = $AC;
        $this->C = new tfrequest($this);
        $this->E = new tfresponse($this);
        $this->A3 = $this->C4();
        $this->A4 = [];
        $this->A8 = [];
    }
    private function AE(&$B1){
        if(isset($B1["@attributes"]) && is_array($B1["@attributes"])){
            $B1 = array_merge($B1, $B1["@attributes"]);
            unset($B1["@attributes"]);
        }
        if(is_array($B1)){
            foreach ($B1 as $B4 => $B5){
                $this->AE($B1[$B4]);
            }
        }
    }
    private function BA(&$B1){

    }
    private function C0(&$B1){
        if(!isset($B1["system"]) || !isset($B1["system"]["serverUrl"])) $B1["system"]["serverUrl"] = "";
    }
    private function C4(): array{
        $C6 = TFPHP_DOCUMENT_ROOT. "/config/tfphp";
        if(file_exists($C6. ".xml")){
            $B1 = simplexml_load_file($C6. ".xml");
            $B1 = json_encode($B1);
            $B1 = json_decode($B1, true);
            $this->AE($B1);
            $this->C0($B1);
            return $B1;
        }
        else if(file_exists($C6. ".yaml")){
            $B1 = yaml_parse_file($C6. ".yaml");
            $this->BA($B1);
            $this->C0($B1);
            return $B1;
        }
        return [];
    }
    public function getRequest(): tfrequest{
        return $this->C;
    }
    public function getResponse(): tfresponse{
        return $this->E;
    }
    public function getConfig(): array{
        return $this->A3;
    }
    public function getDataSource(string $C8=null): ?tfdo{
        if(!$C8) $C8 = "default";
        if(!isset($this->A3["database"][$C8]) || !is_array($this->A3["database"][$C8])){
            return null;
        }
        if(!isset($this->A4[$C8])) $this->A4[$C8] = new tfdo($this, $this->A3["database"][$C8]);
        return $this->A4[$C8];
    }
    public function getRedis(string $C8=null): ?tfredis{
        if(!$C8) $C8 = "default";
        if(!isset($this->A3["redis"][$C8]) || !is_array($this->A3["redis"][$C8])){
            return null;
        }
        if(!isset($this->A8[$C8])) $this->A8[$C8] = new tfredis($this, $this->A3["redis"][$C8]);
        return $this->A8[$C8];
    }
    public function serverURL(string $C9): string{
        return $this->A3["system"]["serverUrl"]. $C9;
    }
    public function URL(string $C9): string{
        return $this->serverURL($C9);
    }
    public function start(){
        $CE = $_SERVER["REQUEST_URI"];
        $CF = strpos($CE, "?");
        if($CF !== false){
            $D4 = substr($CE, 0, $CF);
            $D6 = substr($CE, $CF+1);
        }
        else{
            $D4 = $CE;
            $D6 = "";
        }
        if($D6 != ""){
            $D8 = explode("&", $D6);
            foreach ($D8 as $DB){
                if(($E1 = strpos($DB, "=")) !== false) $_GET[substr($DB, 0, $E1)] = urldecode(substr($DB, $E1+1));
                else $_GET[$DB] = "";
            }
        }
        if(isset($_SERVER["HTTP_CONTENT_TYPE"]) && strpos($_SERVER["HTTP_CONTENT_TYPE"], "application/json") !== false){
            $_POST = json_decode(file_get_contents("php://input"), true);
            if(!is_array($_POST)) $_POST = [];
        }
        if(substr($D4, -1) == "/") $D4 .= "index";
        $E4 = "";
        $E8 = $D4;
        if(($ED = strrpos($D4, ".")) !== false){
            $E4 = strtolower(substr($D4, $ED+1));
            $D4 = substr($D4, 0, $ED);
        }
        if(method_exists($this->A, "getRERoutes")){
            $F2 = call_user_func([$this->A, "getRERoutes"]);
            foreach ($F2 as $F6 => $F9){
                $FB = preg_replace(["/([\/\-])/", "/\{[^\}]*\}/"], ["\\\\\\1", "([^\/]*)"], $F6);
                if(preg_match("/^". $FB. "$/", $E8, $A03)){
                    preg_match_all("/\{([^\}]*)\}/", $F6, $A04);
                    $_SERVER["PATH_ARGV"] = [];
                    for($A07=1;$A07<count($A03);$A07++){
                        $_SERVER["PATH_ARGV"][$A04[1][$A07-1]] = urldecode($A03[$A07]);
                    }
                    $D4 = $F9;
                    break;
                }
            }
        }
        if(method_exists($this->A, "getStaticRoutes")){
            $A0A = call_user_func([$this->A, "getStaticRoutes"]);
            foreach ($A0A as $A0D => $F9){
                if(strpos($D4, $A0D) === 0){
                    $E8 = substr($E8, strlen($A0D));
                    $_SERVER["RESOURCE_NAME"] = TFPHP_DOCUMENT_ROOT. $F9. $E8;
                    $_SERVER["RESOURCE_FILENAME"] = $F9. $E8;
                    $D4 = "/tfphp/_static";
                    break;
                }
            }
        }
        if(substr($D4, 0, 7) == "/tfphp/"){
            $A11 = "tfphp\\framework\\controller\\tfphp\\". substr($D4, 7);
            if(!class_exists($A11)){
                throw new \Exception("class '". $A11. "' is not found", 666011);
            }
            $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $A11), 19). ".inc.php";
            $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
            $_SERVER["REQUEST_URI"] = $CE;
            $_SERVER["PHP_SELF"] = $D4;
            $_SERVER["DOCUMENT_ROOT"] = TFPHP_DOCUMENT_ROOT;
            $_SERVER["DOCUMENT_URI"] = TFPHP_DOCUMENT_ROOT. $_SERVER["PHP_SELF"];
            $_SERVER["RESOURCE_EXTENSION"] = $E4;
            $A12 = new \ReflectionClass($A11);
            $A23 = $A12->getParentClass()->getName();
            $A15 = substr($A23, 13);
            if(!in_array($A15, ["tfpage", "tfapi", "tfrestfulAPI"])){
                throw new \Exception("class '". $A11. "' is invalid", 666012);
            }
            $A1B = $A12->newInstanceArgs([$this]);
            $A1B->load();
            return;
        }
        $A21 = [];
        $A24 = $A26 = $A2C = $A2F = "";
        $A35 = false;
        $A37 = false;
        if(!$A35){
            $A11 = "tfphp\\controller". str_replace("/", "\\", $D4);
            $A21[] = $A11;
            if(class_exists($A11)){
                $A35 = true;
            }
        }
        if(!$A35 && basename($D4)[0] == '_'){
            $A2C = substr(basename($D4), 1);
            $D4 = dirname($D4);
            $A11 = "tfphp\\controller". str_replace("/", "\\", $D4);
            $A21[] = $A11;
            if(class_exists($A11)){
                $A35 = true;
                $A37 = true;
            }
        }
        if(!$A35){
            $A26 = basename($D4);
            $D4 = dirname($D4);
            $A24 = basename($D4);
            $A11 = "tfphp\\controller". str_replace("/", "\\", $D4);
            $A21[] = $A11;
            if(class_exists($A11)){
                $A35 = true;
                $A37 = true;
            }
        }
        if(!$A35){
            throw new \Exception("classes '". implode("', '", $A21). "' are not found", 666023);
        }
        $_SERVER["SCRIPT_FILENAME"] = substr(str_replace("\\", "/", $A11), 16). ".inc.php";
        $_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_FILENAME"];
        $_SERVER["REQUEST_URI"] = $CE;
        $_SERVER["PHP_SELF"] = $D4;
        $_SERVER["DOCUMENT_ROOT"] = TFPHP_DOCUMENT_ROOT;
        $_SERVER["DOCUMENT_URI"] = TFPHP_DOCUMENT_ROOT. $_SERVER["PHP_SELF"];
        $_SERVER["RESOURCE_EXTENSION"] = $E4;
        $A12 = new \ReflectionClass($A11);
        $A23 = $A12->getParentClass()->getName();
        $A39 = substr($A23, 13);
        if(!$A37){
            if(!in_array($A39, ["tfpage", "tfapi", "tfrestfulAPI"])){
                throw new \Exception("class '". $A11. "' is invalid", 666015);
            }
        }
        else{
            if(!in_array($A39, ["tfrestfulAPI"])){
                throw new \Exception("class '" . $A11 . "' is invalid", 666016);
            }
            if($A24 == "") $A24 = basename($D4);
            $_SERVER["RESTFUL_RESOURCE_NAME"] = $A24;
            $_SERVER["RESTFUL_RESOURCE_VALUE"] = $A26;
            $_SERVER["RESTFUL_RESOURCE_FUNCTION"] = $A2C;
        }
        $A1B = $A12->newInstanceArgs([$this]);
        $A1B->load();
        return;
    }
    public static function run(string $A12){
        (new tfphp((new \ReflectionClass($A12))->newInstance()))->start();
    }
}
function tfdumpDebug(string $A3D, string $A43, string $A49, int $A4D, array $A52){
    $A56 = file_get_contents($A49);
    $A57 = preg_match_all("/\n/", $A56, $A04, PREG_OFFSET_CAPTURE);
    $A5D = ($A4D-1 > 6) ? $A04[0][$A4D-1-6][1] : 0;
    $A5F = ($A57-$A4D+1 > 5) ? $A04[0][$A4D-1+5][1] : filesize($A49);
    $A60 = htmlspecialchars(substr($A56, $A5D, $A5F-$A5D));
    $A62 = "";
    foreach ($A52 as $A64) if(isset($A64["file"])) $A62 .= sprintf("<span>%s</span><span style=\"color: #666;\"> in %s line %d</span><br />", ((isset($A64["class"])) ? $A64["class"]. "::". $A64["function"] : $A64["function"]). "()", $A64["file"], $A64["line"]);
    echo sprintf("<h1>%s (%d)</h1><span>in %s line %d</span><br /><pre style=\"border: 1px solid #ccc; padding: 12px;\">%s</pre><p>%s</p><div><a href=\"https://tongfu.net/tag/tfphp.html\">TFPHP</a> v0.6.6</div>", $A43, $A3D, $A49, $A4D, $A60, $A62);
    exit(intval($A3D));
}
function tfdumpError(string $A3D, string $A43, string $A49, int $A4D){
    if(defined("TFPHP_DEBUG") && TFPHP_DEBUG) tfdumpDebug(strval($A3D), $A43, $A49, $A4D, debug_backtrace());
}
function tfdumpException($A6A){
    if(defined("TFPHP_DEBUG") && TFPHP_DEBUG) tfdumpDebug(strval($A6A->getCode()), $A6A->getMessage(), $A6A->getFile(), $A6A->getLine(), $A6A->getTrace());
}
set_error_handler("tfphp\\framework\\tfdumpError", E_ALL);
set_exception_handler("tfphp\\framework\\tfdumpException");
function tfphpAutoload($A12){
    $A11 = str_replace("\\", "/", substr($A12, 16));
    $A6C = TFPHP_ROOT. "/". $A11. ".inc.php";
    if(!file_exists($A6C)){
        return false;
    }
    include_once $A6C;
    return true;
}
function tfprojectAutoload($A12){
    $A11 = str_replace("\\", "/", substr($A12, 6));
    $A6C = TFPHP_DOCUMENT_ROOT. "/". $A11. ".inc.php";
    if(!file_exists($A6C)){
        return false;
    }
    include_once $A6C;
    return true;
}
spl_autoload_register("tfphp\\framework\\tfphpAutoload");
spl_autoload_register("tfphp\\framework\\tfprojectAutoload");