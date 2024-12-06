<?php

namespace tfphp\framework\view;

use tfphp\framework\tfphp;

class tfview{
    protected tfphp $tfphp;
    private string $templateDir;
    private string $compileDir;
    private array $tplVariables;
    private array $tplKeepVariables;
    private string $jsExtraQS;
    private string $cssExtraQS;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $B0 = TFPHP_DOCUMENT_ROOT. "/view/tfview";
        $this->templateDir = $B0. "/template";
        $this->compileDir = $B0. "/compile";
        $this->tplVariables = $this->tplKeepVariables = [];
        $this->jsExtraQS = strval($this->tfphp->getConfig()["view"]["resource"]["css"]["extraQS"]);
        $this->cssExtraQS = strval($this->tfphp->getConfig()["view"]["resource"]["js"]["extraQS"]);
    }
    private function compileEnvEncode(string $B4, string $B5): string{
        $B9 = [$B4, filemtime($B5), filesize($B5)];
        $BF = base64_encode(implode(":", $B9)) ;
        return $BF;
    }
    private function compileEnvDecode(string $BF): array{
        $BF = base64_decode($BF);
        $B9 = explode(":", $BF) ;
        return $B9;
    }
    private function tryCompile(string $C4){
        if($C4[0] == '/') $C4 = substr($C4, 1);
        $C5 = $this->compileDir. "/". $C4 ;
        $B4 = substr($C4, 0, -4) ;
        $B5 = $this->templateDir. "/". $B4 ;
        if(!file_exists($B5)){
            throw new \Exception("template '". $B4. "' is not exists");
        }
        if(!file_exists($C5)){
            $this->doCompile($B4);
            if(!file_exists($C5)){
                throw new \Exception("fail to compile template '". $B4. "'");
            }
        }
        $C7 = file_get_contents($C5) ;
        if(!preg_match("/^<\?php \/\*([^\*]*)\*\//", $C7, $rg)){
            $this->doCompile($B4);
            $C7 = file_get_contents($C5);
            if(!preg_match("/^<\?php \/\*([^\*]*)\*\//", $C7, $rg)){
                throw new \Exception("fail to compile template '". $B4. "'");
            }
        }
        $B9 = $this->compileEnvDecode($rg[1]) ;
        if(!$B9
            || $B9[0] != $B4
            || $B9[1] != filemtime($B5)
            || $B9[2] != filesize($B5)){
            $this->doCompile($B4);
        }
        preg_match_all("/<\?php include\(\"([^\"]+)\"\)\; \?>/", $C7, $rgs);
        foreach ($rgs[1] as $DD){
            $this->tryCompile($DD);
        }
    }
    private function doCompile(string $B4){
        $B5 = $this->templateDir. "/". $B4;
        $C4 = $B4. ".php" ;
        $C5 = $this->compileDir. "/". $C4 ;
        $BF = $this->compileEnvEncode($B4, $B5) ;
        $C8 = file_get_contents($B5) ;
        $CC = $this->doCompileContent($C8) ;
        if(!file_exists(dirname($C5))){
            mkdir(dirname($C5), 0777, true);
        }
        file_put_contents($C5, "<?php /*". $BF. "*/ ?>\r\n". $CC);
    }
    private function doCompileContent(string $D1): string{
        $CC = "";
        $D4 = 0 ;
        while(true){
            $D5 = strpos($D1, "<%", $D4);
            if($D5 !== false){
                $CC .= substr($D1, $D4, $D5-$D4);
                $D6 = strpos($D1, "%>", $D5+2);
                if($D6 !== false){
                    $D8 = trim(substr($D1, $D5+2, $D6-$D5-2));
                    if(preg_match("/include[\s\t]+file\=(?:\'([^\']+)\'|\"([^\"]+)\")/i", $D8, $rg)){
                        $D9 = ($rg[1]) ? $rg[1] : $rg[2];
                        $DA = $D9 ;
                        $DD = $D9. ".php" ;
                        $this->doCompile($DA);
                        $D8 = "include(\"". $DD. "\");" ;
                    }
                    else if(preg_match("/resource[\s\t]+(css|js)\=(?:\'([^\']+)\'|\"([^\"]+)\")/i", $D8, $rg)){
                        $D9 = ($rg[2]) ? $rg[2] : $rg[3];
                        $D8 = "echo \$this->htmlReference". strtoupper($rg[1]). "(\"". $D9. "\");" ;
                    }
                    else if(preg_match("/for[\s\t]+\\$([a-zA-Z0-9_]+)[\s\t]+in[\s\t]+\\$([a-zA-Z0-9_\.]+)/", $D8, $rg)){
                        $E2 = $rg[2];
                        $E4 = $rg[1] ;
                        $this->tplKeepVariables[] = $E4;
                        $D8 = "foreach(". $this->doCompileExpression("\$". $E2). " as \$". $E4. "){" ;
                    }
                    else if(preg_match("/for[\s\t]+\\$([a-zA-Z0-9_]+)[\s\t]+in[\s\t]+range\([\s\t]*(\\$[a-zA-Z0-9_\.]+|[0-9\-\.]+)[\s\t]*,[\s\t]*(\\$[a-zA-Z0-9_\.]+|[0-9\-\.]+)[\s\t]*\)/", $D8, $rg)){
                        $E9 = $rg[2];
                        $EF = $rg[3] ;
                        $E4 = $rg[1] ;
                        $this->tplKeepVariables[] = $E4;
                        $D8 = "for(". $this->doCompileExpression("\$". $E4). "=". $this->doCompileExpression($E9). ";". $this->doCompileExpression("\$". $E4). "<=". $this->doCompileExpression($EF). ";". $this->doCompileExpression("\$". $E4). "+=1){" ;
                    }
                    else if(preg_match("/(if|elseif|else[\s\t]+if)[\s\t]+(.+)/s", $D8, $rg)){
                        $F2 = $rg[1];
                        if(preg_match("/^(elseif|else[\s\t]+if)$/", $F2)){
                            $F2 = "}". $F2;
                        }
                        $F8 = $rg[2] ;
                        $F8 = $this->doCompileExpression($F8) ;
                        $D8 = $F2. "(". $F8. "){" ;
                    }
                    else if(preg_match("/(else)/", $D8)){
                        $D8 = "}else{";
                    }
                    else if(preg_match("/\/(for|if)/", $D8)){
                        $D8 = "}";
                    }
                    else{
                        $D8 = $this->doCompileExpression($D8);
                        $D8 = preg_replace("/^[\s\t\r\n]*\\$/", "echo \$", $D8) ;
                    }
                    $CC .= "<?php ". $D8. " ?>";
                    $D4 = $D6+2;
                }
                else{
                    break;
                }
            }
            else{
                $CC .= substr($D1, $D4);
                break;
            }
        }
        return $CC;
    }
    private function doCompileExpression(string $F8): string{
        $F8 = preg_replace_callback("/\\$([a-zA-Z0-9_\.]+)/", function(array $FA){
            $A00 = $FA[1];
            $A05 = explode(".", $A00) ;
            $A07 = count($A05) ;
            if(!in_array($A05[0], $this->tplKeepVariables)) $A0C = "\$this->tplVariables[\"". $A05[0]. "\"]";
            else $A0C = "\$". $A05[0]. "";
            if($A07 > 1){
                for($A0D=1;$A0D<$A07;$A0D++){
                    $A0C .= "[\"". $A05[$A0D]. "\"]";
                }
            }
            return $A0C;
        }, $F8);
        return $F8;
    }
    private function doLoad(string $C4): bool{
        $C5 = $this->compileDir. $C4;
        header("Content-Type: text/html; charset=UTF-8");
        include_once ($C5);
        return true;
    }
    public function setTemplateDir(string $D){
        return $this->templateDir = $D;
    }
    public function setCompileDir(string $A0){
        return $this->compileDir = $A0;
    }
    public function setVar(string $A00, $A12){
        $this->tplVariables[$A00] = $A12;
    }
    public function htmlReferenceJS(string $D9): string{
        if($this->jsExtraQS) $D9 .= ((strpos($D9, "?") === false) ? "?" : "&"). $this->jsExtraQS;
        return sprintf("<script type=\"text/javascript\" src=\"%s\"></script>", $D9);
    }
    public function htmlReferenceCSS(string $D9): string{
        if($this->cssExtraQS) $D9 .= ((strpos($D9, "?") === false) ? "?" : "&"). $this->cssExtraQS;
        return sprintf("<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\" />", $D9);
    }
    public function load(): bool{
        $A13 = str_replace("", "", substr($_SERVER["SCRIPT_FILENAME"], 0, -8));
        $B4 = $A13. ".html" ;
        $C4 = $B4. ".php" ;
        $this->tryCompile($C4);
        chdir($this->compileDir);
        return $this->doLoad($C4);
    }
}