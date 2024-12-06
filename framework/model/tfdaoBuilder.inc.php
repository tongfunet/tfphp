<?php

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoBuilder{
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
    private function makeClassCode(string $B, string $D, string $A1, string $A7, array $AC, array $B0, ?string $B2){
        $B8 = "";
        foreach ($AC as $BA => $fieldParams){
            $BC = "";
            foreach ($fieldParams as $C0 => $paramValue){
                if($C0[0] == '@'){
                    $BC .= sprintf('"%s"=>%s,', substr($C0, 1), $paramValue);
                }
                else{
                    $BC .= sprintf('"%s"=>\"%s\",', $C0, $paramValue);
                }
            }
            if($BC != ""){
                $BC = substr($BC, 0, -1);
            }
            $B8 .= sprintf('                "%s"=>[%s],', $BA, $BC). "\n";
        }
        if($B8 != ""){
            $B8 = substr($B8, 0, -2);
        }
        $C4 = "" ;
        foreach ($B0 as $C6 => $A07){
            $CC = "";
            foreach ($A07 as $constraintField){
                $CC .= sprintf('"%s",', $constraintField);
            }
            if($CC != ""){
                $CC = substr($CC, 0, -1);
            }
            $C4 .= sprintf('                "%s"=>[%s],', $C6, $CC). "\n";
        }
        if($C4 != ""){
            $C4 = substr($C4, 0, -2);
            $C4 = ",\n". sprintf('            "constraints"=>[
%s
            ]', $C4) ;
        }
        $CE = "" ;
        if($B2){
            $CE = ",\n". sprintf('            "autoIncrementField"=>"%s"', $B2);
        }
        $D3 = "" ;
        if($B){
            $D3 .= ",\n". sprintf('            "dataSource"=>"%s"', $B);
        }
        return sprintf('<?php

namespace tfphp\model\dao%s;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfdao;
use tfphp\framework\model\tfdaoSingle;

class %s extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"%s",
            "fields"=>[
%s
            ]%s%s%s
        ]);
    }
}', "\\". $D, $A7, $A7, $B8, $C4, $CE, $D3);
    }
    public function build(string $D8): ?array{
        $B = ($D8) ? $D8 : "default";
        $D = ($B == "default") ? "__". $B. "__" : $B ;
        $DE = $this->tfphp->getDataSource($B) ;
        if(!$DE){
            return null;
        }
        $E2 = $DE->fetchAll("show tables", []) ;
        $E5 = [] ;
        foreach ($E2 as $tableInfo){
            foreach ($tableInfo as $E6 => $value){
                if(substr($E6, 0, 10) == "Tables_in_"){
                    $A1 = substr($E6, 10);
                    $A7 = $value ;
                    $AC = [] ;
                    $B0 = [] ;
                    $B2 = null ;
                    $E8 = $DE->fetchAll("show create table `". $A7. "`", []) ;
                    $E9 = $E8[0] ;
                    $EA = $E9["Table"] ;
                    $ED = $E9["Create Table"] ;
                    $F2 = explode("\n", $ED) ;
                    for($F7=0;$F7<count($F2);$F7++){
                        $F9 = $F2[$F7];
                        if(preg_match("/^[\s\t]*\`([^\`]+)\`[\s\t]*([a-z0-9]+)/", $F9, $rg)){
                            $BA = $rg[1];
                            $FA = $rg[2] ;
                            $FF = false ;
                            if(preg_match("/^(tinyint|smallint|mediumint|int|bigint)/i", $FA)){
                                $FA = "tfdao::FIELD_TYPE_INT";
                                if(preg_match("/[\s\t]AUTO_INCREMENT[\s\t\r\n,]/", $F9)){
                                    $FF = true;
                                    $B2 = $BA ;
                                }
                            }
                            else{
                                $FA = "tfdao::FIELD_TYPE_STR";
                            }
                            $A00 = false ;
                            if(preg_match("/[\s\t]+not[\s\t]+null/i", $F9) && !preg_match("/[\s\t]+default[\s\t]+/i", $F9) && !$FF){
                                $A00 = true;
                            }
                            $AC[$BA] = ["@type"=>$FA];
                            if($A00){
                                $AC[$BA]["@required"] = "true";
                            }
                        }
                        else if(preg_match("/^[\s\t]*(PRIMARY KEY|UNIQUE KEY|KEY)?([^\(]+)\(([^\)]+)\)/", $F9, $rg)){
                            $A01 = trim($rg[1]);
                            if(!in_array($A01, ["KEY"])){
                                $C6 = str_replace("`", "", trim($rg[2]));
                                if($C6 == ""){
                                    $C6 = "default";
                                }
                                $A07 = explode(",", str_replace("`", "", $rg[3])) ;
                                $B0[$C6] = $A07;
                            }
                        }
                    }
                    $this->makeClassCode($B, $D, $A1, $A7, $AC, $B0, $B2);
                    $A0C = TFPHP_DOCUMENT_ROOT. "/model/dao/". $D. "/". $A7. ".inc.php" ;
                    $A0D = dirname($A0C) ;
                    $A12 = true ;
                    if(!file_exists($A0D)){
                        $A12 = mkdir($A0D, 0777);
                    }
                    $A14 = file_put_contents($A0C, $this->makeClassCode($B, $D, $A1, $A7, $AC, $B0, $B2)) ;
                    $E5[] = [
                        "ds"=>$B,
                        "db"=>$A1,
                        "table"=>$A7,
                        "result"=>($A12 && $A14 > 0)
                    ];
                }
            }
        }
        return $E5;
    }
}