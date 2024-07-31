<?php

namespace tfphp\framework\view;

use tfphp\framework\tfphp;

class tfview{
    private tfphp $tfphp;
    private string $templateDir;
    private string $compileDir;
    private array $_variables;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $tfviewDir = TFPHP_ROOT. "/view/tfview";
        $this->templateDir = $tfviewDir. "/template";
        $this->compileDir = $tfviewDir. "/compile";
        $this->_variables = [];
    }
    private function readFileFirstLine(string $filepath): string{
        $fo = fopen($filepath, "r");
        $line = fgets($fo);
        fclose($fo);
        return $line;
    }
    private function doCompile(string $templateFilepath, string $compileFilepath){
        $compileEnv = [
            "filepath"=>$templateFilepath,
            "filemtime"=>filemtime($templateFilepath),
            "filesize"=>filesize($templateFilepath),
        ];
        $compileEnvLine = base64_encode(serialize($compileEnv));
        $templateContent = file_get_contents($templateFilepath);
        $compileContent = "";
        $p = 0;
        while(true){
           $p1 = strpos($templateContent, "<%", $p);
           if($p1 !== false){
               $compileContent .= substr($templateContent, $p, $p1-$p);
               $p2 = strpos($templateContent, "%>", $p1+2);
               if($p2 !== false){
                   $code = substr($templateContent, $p1+2, $p2-$p1-2);
//                   var_dump($code);
                   $code = preg_replace_callback("/\\$([a-zA-Z0-9_\.]+)/", function(array $mats){
                       $varNames = explode(".", $mats[1]);
                       $compileVarName = "\$this->_variables";
                       foreach ($varNames as $varName){
                           $compileVarName .= "[\"". $varName. "\"]";
                       }
                       return $compileVarName;
                   }, $code);
                   $code = preg_replace("/^[\s\t\r\n]*\\$/", "echo \$", $code);
//                   var_dump($code);
                   $compileContent .= "<?php ". $code. " ?>";
                   $p = $p2+2;
               }
               else{
                   break;
               }
           }
           else{
               $compileContent .= substr($templateContent, $p);
               break;
           }
        }
        file_put_contents($compileFilepath, "<?php /*". $compileEnvLine. "*/ ?>". $compileContent);
    }
    private function doLoad(string $compileFilepath): bool{
        header("Content-Type: text/html; charset=UTF-8");
        include_once ($compileFilepath);
        return true;
    }
    public function setTemplateDir(string $templateDir){
        return $this->templateDir = $templateDir;
    }
    public function setCompileDir(string $compileDir){
        return $this->compileDir = $compileDir;
    }
    public function setVar(string $varName, $varValue){
        $this->_variables[$varName] = $varValue;
    }
    public function load(): bool{
        $templateFilename = str_replace("", "", substr($_SERVER["SCRIPT_FILENAME"], 0, -8));
        $templateFilepath = $this->templateDir. $templateFilename. ".html";
        $compileFilepath = $this->compileDir. $templateFilename. ".php";
        if(!file_exists($compileFilepath)){
            $this->doCompile($templateFilepath, $compileFilepath);
            return $this->doLoad($compileFilepath);
        }
        $compileFirstLine = $this->readFileFirstLine($compileFilepath);
        if(!preg_match("/^<\?php \/\*([^\*]*)\*\//", $compileFirstLine, $rg)){
            $this->doCompile($templateFilepath, $compileFilepath);
            return $this->doLoad($compileFilepath);
        }
        $compileEnv = unserialize(base64_decode($rg[1]));
        if(!$compileEnv
            || $compileEnv["filepath"] != $templateFilepath
            || $compileEnv["filemtime"] != filemtime($templateFilepath)
            || $compileEnv["filesize"] != filesize($templateFilepath)){
            $this->doCompile($templateFilepath, $compileFilepath);
            return $this->doLoad($compileFilepath);
        }
        return $this->doLoad($compileFilepath);
    }
}