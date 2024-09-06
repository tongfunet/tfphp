<?php

namespace tfphp\framework\view;

use tfphp\framework\tfphp;
use tfproject\model\article;

class tfview{
    protected tfphp $tfphp;
    private string $templateDir;
    private string $compileDir;
    private array $tplVariables;
    private array $tplKeepVariables;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $tfviewDir = TFPHP_DOCUMENT_ROOT. "/view/tfview";
        $this->templateDir = $tfviewDir. "/template";
        $this->compileDir = $tfviewDir. "/compile";
        $this->tplVariables = $this->tplKeepVariables = [];
    }
    private function readFileFirstLine(string $filepath): string{
        $fo = fopen($filepath, "r");
        $line = fgets($fo);
        fclose($fo);
        return $line;
    }
    private function compileEnvEncode(string $templateBasepath, string $templateFilepath): string{
        $compileEnv = [$templateBasepath, filemtime($templateFilepath), filesize($templateFilepath)];
        $compileEnvLine = base64_encode(implode(":", $compileEnv));
        return $compileEnvLine;
    }
    private function compileEnvDecode(string $compileEnvLine): array{
        $compileEnvLine = base64_decode($compileEnvLine);
        $compileEnv = explode(":", $compileEnvLine);
        return $compileEnv;
    }
    private function tryCompile(string $compileBasepath){
        if($compileBasepath[0] == '/') $compileBasepath = substr($compileBasepath, 1);
        $compileFilepath = $this->compileDir. "/". $compileBasepath;
        $templateBasepath = substr($compileBasepath, 0, -4);
        $templateFilepath = $this->templateDir. "/". $templateBasepath;
        if(!file_exists($templateFilepath)){
            throw new \Exception("template '". $templateBasepath. "' is not exists");
        }
        if(!file_exists($compileFilepath)){
            $this->doCompile($templateBasepath);
            if(!file_exists($compileFilepath)){
                throw new \Exception("fail to compile template '". $templateBasepath. "'");
            }
        }
        $compileFilecontent = file_get_contents($compileFilepath);
        if(!preg_match("/^<\?php \/\*([^\*]*)\*\//", $compileFilecontent, $rg)){
            $this->doCompile($templateBasepath);
            $compileFilecontent = file_get_contents($compileFilepath);
            if(!preg_match("/^<\?php \/\*([^\*]*)\*\//", $compileFilecontent, $rg)){
                throw new \Exception("fail to compile template '". $templateBasepath. "'");
            }
        }
        $compileEnv = $this->compileEnvDecode($rg[1]);
        if(!$compileEnv
            || $compileEnv[0] != $templateBasepath
            || $compileEnv[1] != filemtime($templateFilepath)
            || $compileEnv[2] != filesize($templateFilepath)){
            $this->doCompile($templateBasepath);
        }
        preg_match_all("/<\?php include\(\"([^\"]+)\"\)\; \?>/", $compileFilecontent, $rgs);
        foreach ($rgs[1] as $myCompileBasepath){
            $this->tryCompile($myCompileBasepath);
        }
    }
    private function doCompile(string $templateBasepath){
        $templateFilepath = $this->templateDir. "/". $templateBasepath;
        $compileBasepath = $templateBasepath. ".php";
        $compileFilepath = $this->compileDir. "/". $compileBasepath;
        $compileEnvLine = $this->compileEnvEncode($templateBasepath, $templateFilepath);
        $templateContent = file_get_contents($templateFilepath);
        $compileContent = $this->doCompileContent($templateContent);
        if(!file_exists(dirname($compileFilepath))){
            mkdir(dirname($compileFilepath), 0777, true);
        }
        file_put_contents($compileFilepath, "<?php /*". $compileEnvLine. "*/ ?>\r\n". $compileContent);
    }
    private function doCompileContent(string $content): string{
        $compileContent = "";
        $p = 0;
        while(true){
            $p1 = strpos($content, "<%", $p);
            if($p1 !== false){
                $compileContent .= substr($content, $p, $p1-$p);
                $p2 = strpos($content, "%>", $p1+2);
                if($p2 !== false){
                    $code = trim(substr($content, $p1+2, $p2-$p1-2));
                    if(preg_match("/include[\s\t]+file\=(?:\'([^\']+)\'|\"([^\"]+)\")/i", $code, $rg)){
                        $filepath = ($rg[1]) ? $rg[1] : $rg[2];
                        $myTemplateBasepath = $filepath;
                        $myCompileBasepath = $filepath. ".php";
                        $this->doCompile($myTemplateBasepath);
                        $code = "include(\"". $myCompileBasepath. "\");";
                    }
                    else if(preg_match("/for[\s\t]+\\$([a-zA-Z0-9_]+)[\s\t]+in[\s\t]+\\$([a-zA-Z0-9_\.]+)/", $code, $rg)){
                        $itemsName = $rg[2];
                        $itemKeyName = $rg[1];
                        $this->tplKeepVariables[] = $itemKeyName;
                        $code = "foreach(". $this->doCompileExpression("\$". $itemsName). " as \$". $itemKeyName. "){";
                    }
                    else if(preg_match("/for[\s\t]+\\$([a-zA-Z0-9_]+)[\s\t]+in[\s\t]+range\([\s\t]*(\\$[a-zA-Z0-9_\.]+|[0-9\-\.]+)[\s\t]*,[\s\t]*(\\$[a-zA-Z0-9_\.]+|[0-9\-\.]+)[\s\t]*\)/", $code, $rg)){
                        $itemRangeFromName = $rg[2];
                        $itemRangeToName = $rg[3];
                        $itemKeyName = $rg[1];
                        $this->tplKeepVariables[] = $itemKeyName;
                        $code = "for(". $this->doCompileExpression("\$". $itemKeyName). "=". $this->doCompileExpression($itemRangeFromName). ";". $this->doCompileExpression("\$". $itemKeyName). "<=". $this->doCompileExpression($itemRangeToName). ";". $this->doCompileExpression("\$". $itemKeyName). "+=1){";
                    }
                    else if(preg_match("/(if|elseif|else[\s\t]+if)[\s\t]+(.+)/s", $code, $rg)){
                        $keyword = $rg[1];
                        if(preg_match("/^(elseif|else[\s\t]+if)$/", $keyword)){
                            $keyword = "}". $keyword;
                        }
                        $expression = $rg[2];
                        $expression = $this->doCompileExpression($expression);
                        $code = $keyword. "(". $expression. "){";
                    }
                    else if(preg_match("/(else)/", $code)){
                        $code = "}else{";
                    }
                    else if(preg_match("/\/(for|if)/", $code)){
                        $code = "}";
                    }
                    else{
                        $code = $this->doCompileExpression($code);
                        $code = preg_replace("/^[\s\t\r\n]*\\$/", "echo \$", $code);
                    }
                    $compileContent .= "<?php ". $code. " ?>";
                    $p = $p2+2;
                }
                else{
                    break;
                }
            }
            else{
                $compileContent .= substr($content, $p);
                break;
            }
        }
        return $compileContent;
    }
    private function doCompileExpression(string $expression): string{
        $expression = preg_replace_callback("/\\$([a-zA-Z0-9_\.]+)/", function(array $mats){
            $varName = $mats[1];
            $varNameItems = explode(".", $varName);
            $varNameItemsCount = count($varNameItems);
            if(!in_array($varNameItems[0], $this->tplKeepVariables)) $compileVarName = "\$this->tplVariables[\"". $varNameItems[0]. "\"]";
            else $compileVarName = "\$". $varNameItems[0]. "";
            if($varNameItemsCount > 1){
                for($i=1;$i<$varNameItemsCount;$i++){
                    $compileVarName .= "[\"". $varNameItems[$i]. "\"]";
                }
            }
            return $compileVarName;
        }, $expression);
        return $expression;
    }
    private function doLoad(string $compileBasepath): bool{
        $compileFilepath = $this->compileDir. $compileBasepath;
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
        $this->tplVariables[$varName] = $varValue;
    }
    public function load(): bool{
        $templateFilename = str_replace("", "", substr($_SERVER["SCRIPT_FILENAME"], 0, -8));
        $templateBasepath = $templateFilename. ".html";
        $compileBasepath = $templateBasepath. ".php";
        $this->tryCompile($compileBasepath);
        chdir($this->compileDir);
        return $this->doLoad($compileBasepath);
    }
}