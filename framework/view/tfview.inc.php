<?php 

namespace tfphp\framework\view;

use tfphp\framework\tfphp;

class tfview{
    protected tfphp $tfphp;
    private string $A4;
    private string $A7;
    private array $A8;
    private array $AD;
    private string $AF;
    private string $B5;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $BF = TFPHP_DOCUMENT_ROOT. "/view/tfview";
        $this->A4 = $BF. "/template";
        $this->A7 = $BF. "/compile";
        $this->A8 = $this->AD = [];
        $this->AF = strval($this->tfphp->getConfig()["view"]["resource"]["css"]["extraQS"]);
        $this->B5 = strval($this->tfphp->getConfig()["view"]["resource"]["js"]["extraQS"]);
    }
    private function C1(string $C2, string $C4): string{
        $C5 = [$C2, filemtime($C4), filesize($C4)];
        $CA = base64_encode(implode(":", $C5));
        return $CA;
    }
    private function D0(string $CA): array{
        $CA = base64_decode($CA);
        $C5 = explode(":", $CA);
        return $C5;
    }
    private function D1(string $D5){
        if($D5[0] == '/') $D5 = substr($D5, 1);
        $DB = $this->A7. "/". $D5;
        $C2 = substr($D5, 0, -4);
        $C4 = $this->A4. "/". $C2;
        if(!file_exists($C4)){
            throw new \Exception("template '". $C2. "' is not exists", 666071);
        }
        if(!file_exists($DB)){
            $this->F2($C2);
            if(!file_exists($DB)){
                throw new \Exception("fail to compile template '". $C2. "'", 666072);
            }
        }
        $E1 = file_get_contents($DB);
        if(!preg_match("/^<\?php \/\*([^\*]*)\*\//", $E1, $E4)){
            $this->F2($C2);
            $E1 = file_get_contents($DB);
            if(!preg_match("/^<\?php \/\*([^\*]*)\*\//", $E1, $E4)){
                throw new \Exception("fail to compile template '". $C2. "'", 666072);
            }
        }
        $C5 = $this->D0($E4[1]);
        if(!$C5
            || $C5[0] != $C2
            || $C5[1] != filemtime($C4)
            || $C5[2] != filesize($C4)){
            $this->F2($C2);
        }
        preg_match_all("/<\?php include\(\"([^\"]+)\"\)\; \?>/", $E1, $EA);
        foreach ($EA[1] as $F0){
            $this->D1($F0);
        }
    }
    private function F2(string $C2){
        $C4 = $this->A4. "/". $C2;
        $D5 = $C2. ".php";
        $DB = $this->A7. "/". $D5;
        $CA = $this->C1($C2, $C4);
        $F7 = file_get_contents($C4);
        $F9 = $this->FC($F7);
        if(!file_exists(dirname($DB))){
            mkdir(dirname($DB), 0777, true);
        }
        file_put_contents($DB, "<?php /*". $CA. "*/ ?>\r\n". $F9);
    }
    private function FC(string $A04): string{
        $F9 = "";
        $A07 = 0;
        while(true){
            $A0B = strpos($A04, "<%", $A07);
            if($A0B !== false){
                $F9 .= substr($A04, $A07, $A0B-$A07);
                $A10 = strpos($A04, "%>", $A0B+2);
                if($A10 !== false){
                    $A13 = trim(substr($A04, $A0B+2, $A10-$A0B-2));
                    // include file='xxx'
                    if(preg_match("/include[\s\t]+file\=(?:\'([^\']+)\'|\"([^\"]+)\")/i", $A13, $E4)){
                        $A17 = ($E4[1]) ? $E4[1] : $E4[2];
                        $A1A = $A17;
                        $F0 = $A17. ".php";
                        $this->F2($A1A);
                        $A13 = "include(\"". $F0. "\");";
                    }
                    // resource css='xxx'
                    // resource js='xxx'
                    else if(preg_match("/resource[\s\t]+(css|js)\=(?:\'([^\']+)\'|\"([^\"]+)\")/i", $A13, $E4)){
                        $A17 = ($E4[2]) ? $E4[2] : $E4[3];
                        $A13 = "echo \$this->htmlReference". strtoupper($E4[1]). "(\"". $A17. "\");";
                    }
                    // for $item in $items
                    else if(preg_match("/for[\s\t]+\\$([a-zA-Z0-9_]+)[\s\t]+in[\s\t]+\\$([a-zA-Z0-9_\.]+)/", $A13, $E4)){
                        $A1B = $E4[2];
                        $A1F = $E4[1];
                        $this->AD[] = $A1F;
                        $A13 = "foreach(". $this->A39("\$". $A1B). " as \$". $A1F. "){";
                    }
                    // for $item in range($from, $to)
                    else if(preg_match("/for[\s\t]+\\$([a-zA-Z0-9_]+)[\s\t]+in[\s\t]+range\([\s\t]*(\\$[a-zA-Z0-9_\.]+|[0-9\-\.]+)[\s\t]*,[\s\t]*(\\$[a-zA-Z0-9_\.]+|[0-9\-\.]+)[\s\t]*\)/", $A13, $E4)){
                        $A23 = $E4[2];
                        $A29 = $E4[3];
                        $A1F = $E4[1];
                        $this->AD[] = $A1F;
                        $A13 = "for(". $this->A39("\$". $A1F). "=". $this->A39($A23). ";". $this->A39("\$". $A1F). "<=". $this->A39($A29). ";". $this->A39("\$". $A1F). "+=1){";
                    }
                    // if xxx
                    // elseif xxx
                    // else if xxx
                    else if(preg_match("/(if|elseif|else[\s\t]+if)[\s\t]+(.+)/s", $A13, $E4)){
                        $A2E = $E4[1];
                        if(preg_match("/^(elseif|else[\s\t]+if)$/", $A2E)){
                            $A2E = "}". $A2E;
                        }
                        $A34 = $E4[2];
                        $A34 = $this->A39($A34);
                        $A13 = $A2E. "(". $A34. "){";
                    }
                    // else
                    else if(preg_match("/(else)/", $A13)){
                        $A13 = "}else{";
                    }
                    else if(preg_match("/\/(for|if)/", $A13)){
                        $A13 = "}";
                    }
                    // $tfphp, $get, $post, $files, $cookie, $server, $session
                    else if(preg_match("/\\$(tfphp|get|post|files|cookie|server|session)(.+)/", $A13, $E4)){
                        $A34 = $E4[2];
                        $A34 = $this->A39($A34);
                        $A13 = "echo \$this->getTFPHP()";
                        switch ($E4[1]){
                            case "get":
                                $A13 .= "->getRequest()->get()";
                                break;
                            case "post":
                                $A13 .= "->getRequest()->post()";
                                break;
                            case "files":
                                $A13 .= "->getRequest()->files()";
                                break;
                            case "cookie":
                                $A13 .= "->getRequest()->cookie()";
                                break;
                            case "server":
                                $A13 .= "->getRequest()->server()";
                                break;
                            case "session":
                                $A13 .= "->getRequest()->session()";
                                break;
                        }
                        $A13 .= $A34;
                    }
                    // serverURL('xxx')
                    // URL('xxx')
                    else if(preg_match("/(serverURL|URL)(.+)/", $A13, $E4)){
                        $A34 = $E4[2];
                        $A34 = $this->A39($A34);
                        $A13 = "echo \$this->". $E4[1]. $A34;
                    }
                    else{
                        $A13 = $this->A39($A13);
                        $A13 = preg_replace("/^[\s\t\r\n]*\\$/", "echo \$", $A13);
                    }
                    $F9 .= "<?php ". $A13. " ?>";
                    $A07 = $A10+2;
                }
                else{
                    break;
                }
            }
            else{
                $F9 .= substr($A04, $A07);
                break;
            }
        }
        return $F9;
    }
    private function A39(string $A34): string{
        $A34 = preg_replace_callback("/\\$([a-zA-Z0-9_\.]+)/", function(array $A3E){
            $A42 = $A3E[1];
            $A44 = explode(".", $A42);
            $A4A = count($A44);
            $A4C = (!in_array($A44[0], $this->AD)) ? "\$this->getVar(\"". $A44[0]. "\")" : "\$". $A44[0]. "";
            if($A4A > 1){
                for($A4F=1;$A4F<$A4A;$A4F++){
                    $A4C .= ($A44[$A4F]) ? "[\"". $A44[$A4F]. "\"]" : ".";
                }
            }
            return $A4C;
        }, $A34);
        return $A34;
    }
    private function A55(string $D5): bool{
        $DB = $this->A7. $D5;
        header("Content-Type: text/html; charset=UTF-8");
        include_once ($DB);
        return true;
    }
    public function setTemplateDir(string $A57){
        return $this->A4 = $A57;
    }
    public function setCompileDir(string $A5D){
        return $this->A7 = $A5D;
    }
    public function setVar(string $A42, $A60){
        $this->A8[$A42] = $A60;
    }
    public function getVar(string $A42){
        return (isset($this->A8[$A42])) ? $this->A8[$A42] : null;
    }
    public function htmlReferenceJS(string $A17): string{
        if($this->AF) $A17 .= ((strpos($A17, "?") === false) ? "?" : "&"). $this->AF;
        if(!preg_match("/^([^\:]+)\:\/\//", $A17)) $A17 = $this->tfphp->URL($A17);
        return sprintf("<script type=\"text/javascript\" src=\"%s\"></script>", $A17);
    }
    public function htmlReferenceCSS(string $A17): string{
        if($this->B5) $A17 .= ((strpos($A17, "?") === false) ? "?" : "&"). $this->B5;
        if(!preg_match("/^([^\:]+)\:\/\//", $A17)) $A17 = $this->tfphp->URL($A17);
        return sprintf("<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\" />", $A17);
    }
    public function getTFPHP(): tfphp{
        return $this->tfphp;
    }
    public function serverURL(string $A61): string{
        return $this->tfphp->serverURL($A61);
    }
    public function URL(string $A61): string{
        return $this->serverURL($A61);
    }
    public function load(): bool{
        $A67 = str_replace("", "", substr($_SERVER["SCRIPT_FILENAME"], 0, -8));
        $C2 = $A67. ".html";
        $D5 = $C2. ".php";
        $this->D1($D5);
        chdir($this->A7);
        return $this->A55($D5);
    }
}