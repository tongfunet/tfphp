<?php 

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoBuilder{
    protected tfphp $tfphp;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
    }
    private function A3(string $A5, string $A7, string $AC, string $AF, string $B1, array $B5, array $B6, ?string $BC){
        $C1 = "";
        foreach ($B5 as $C4 => $C5){
            $CB = "";
            foreach ($C5 as $D0 => $D5){
                if($D0[0] == '@'){
                    $CB .= sprintf('"%s"=>%s,', substr($D0, 1), $D5);
                }
                else{
                    $CB .= sprintf('"%s"=>\"%s\",', $D0, $D5);
                }
            }
            if($CB != ""){
                $CB = substr($CB, 0, -1);
            }
            $C1 .= sprintf('                "%s"=>[%s],', $C4, $CB). "\n";
        }
        if($C1 != ""){
            $C1 = substr($C1, 0, -2);
        }
        $D6 = "";
        foreach ($B6 as $DC => $E1){
            $E3 = "";
            foreach ($E1 as $E4){
                $E3 .= sprintf('"%s",', $E4);
            }
            if($E3 != ""){
                $E3 = substr($E3, 0, -1);
            }
            $D6 .= sprintf('                "%s"=>[%s],', $DC, $E3). "\n";
        }
        if($D6 != ""){
            $D6 = substr($D6, 0, -2);
            $D6 = ",\n". sprintf('            "constraints"=>[
%s
            ]', $D6);
        }
        $E8 = "";
        if($BC){
            $E8 = ",\n". sprintf('            "autoIncrementField"=>"%s"', $BC);
        }
        $EC = "";
        if($A5){
            $EC .= ",\n". sprintf('            "dataSource"=>"%s"', $A5);
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
}', "\\". $A7, $B1, $AF, $C1, $D6, $E8, $EC);
    }
    public function build(string $F0): ?array{
        $A5 = ($F0) ? $F0 : "default";
        $A7 = ($A5 == "default") ? "__". $A5. "__" : $A5;
        $F4 = $this->tfphp->getConfig()["database"][$A5];
        $F7 = $this->tfphp->getDataSource($A5);
        if(!$F7){
            return null;
        }
        $F9 = $F7->fetchAll("show tables", []);
        $FF = [];
        foreach ($F9 as $A03){
            foreach ($A03 as $A08 => $A0A){
                if(substr($A08, 0, 10) == "Tables_in_"){
                    $AC = substr($A08, 10);
                    $AF = $A0A;
                    $B1 = (isset($F4["table_prefix"]) && $F4["table_prefix"] && strpos($AF, $F4["table_prefix"]) === 0) ? substr($AF, strlen($F4["table_prefix"])) : $AF;
                    $B5 = [];
                    $B6 = [];
                    $BC = null;
                    $A0D = $F7->fetchAll("show create table `". $AF. "`", []);
                    $A13 = $A0D[0];
                    $A17 = $A13["Table"];
                    $A1C = $A13["Create Table"];
                    $A1D = explode("\n", $A1C);
                    for($A20=0;$A20<count($A1D);$A20++){
                        $A21 = $A1D[$A20];
                        if(preg_match("/^[\s\t]*\`([^\`]+)\`[\s\t]*([a-z0-9]+)/", $A21, $A22)){
                            $C4 = $A22[1];
                            $A28 = $A22[2];
                            $A2E = false;
                            if(preg_match("/^(tinyint|smallint|mediumint|int|bigint)/i", $A28)){
                                $A28 = "tfdao::FIELD_TYPE_INT";
                                if(preg_match("/[\s\t]AUTO_INCREMENT[\s\t\r\n,]/", $A21)){
                                    $A2E = true;
                                    $BC = $C4;
                                }
                            }
                            else{
                                $A28 = "tfdao::FIELD_TYPE_STR";
                            }
                            $A30 = false;
                            if(preg_match("/[\s\t]+not[\s\t]+null/i", $A21) && !preg_match("/[\s\t]+default[\s\t]+/i", $A21) && !$A2E){
                                $A30 = true;
                            }
                            $B5[$C4] = ["@type"=>$A28];
                            if($A30){
                                $B5[$C4]["@required"] = "true";
                            }
                        }
                        else if(preg_match("/^[\s\t]*(PRIMARY KEY|UNIQUE KEY|KEY)?([^\(]+)\(([^\)]+)\)/", $A21, $A22)){
                            $A33 = trim($A22[1]);
                            if(!in_array($A33, ["KEY"])){
                                $DC = str_replace("`", "", trim($A22[2]));
                                if($DC == ""){
                                    $DC = "default";
                                }
                                $E1 = explode(",", str_replace("`", "", $A22[3]));
                                $B6[$DC] = $E1;
                            }
                        }
                    }
                    $this->A3($A5, $A7, $AC, $AF, $B1, $B5, $B6, $BC);
                    $A39 = TFPHP_DOCUMENT_ROOT. "/model/dao/". $A7. "/". $B1. ".inc.php";
                    $A3F = dirname($A39);
                    $A42 = true;
                    if(!file_exists($A3F)){
                        $A42 = mkdir($A3F, 0777, true);
                    }
                    $A46 = file_put_contents($A39, $this->A3($A5, $A7, $AC, $AF, $B1, $B5, $B6, $BC));
                    $FF[] = [
                        "ds"=>$A5,
                        "db"=>$AC,
                        "table"=>$AF,
                        "result"=>($A42 && $A46 > 0)
                    ];
                }
            }
        }
        return $FF;
    }
}