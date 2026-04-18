<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

class tfdaoBuilder{
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
    private function makeClassCode(string $dsName, string $dsNameForPath, string $dbName, string $tableName, string $tableNameForPath, array $fields, array $constraints, ?string $autoIncrementField){
        $fieldsCode = "";
        foreach ($fields as $fieldName => $fieldParams){
            $fieldParamsCode = "";
            foreach ($fieldParams as $paramName => $paramValue){
                if($paramName[0] == '@'){
                    $fieldParamsCode .= sprintf('"%s"=>%s,', substr($paramName, 1), $paramValue);
                }
                else{
                    $fieldParamsCode .= sprintf('"%s"=>\"%s\",', $paramName, $paramValue);
                }
            }
            if($fieldParamsCode != ""){
                $fieldParamsCode = substr($fieldParamsCode, 0, -1);
            }
            $fieldsCode .= sprintf('                "%s"=>[%s],', $fieldName, $fieldParamsCode). "\n";
        }
        if($fieldsCode != ""){
            $fieldsCode = substr($fieldsCode, 0, -2);
        }
        $constraintsCode = "";
        foreach ($constraints as $constraintName => $constraintFields){
            $constraintFieldsCode = "";
            foreach ($constraintFields as $constraintField){
                $constraintFieldsCode .= sprintf('"%s",', $constraintField);
            }
            if($constraintFieldsCode != ""){
                $constraintFieldsCode = substr($constraintFieldsCode, 0, -1);
            }
            $constraintsCode .= sprintf('                "%s"=>[%s],', $constraintName, $constraintFieldsCode). "\n";
        }
        if($constraintsCode != ""){
            $constraintsCode = substr($constraintsCode, 0, -2);
            $constraintsCode = ",\n". sprintf('            "constraints"=>[
%s
            ]', $constraintsCode);
        }
        $autoIncrementCode = "";
        if($autoIncrementField){
            $autoIncrementCode = ",\n". sprintf('            "autoIncrementField"=>"%s"', $autoIncrementField);
        }
        $dataSourceCode = "";
        if($dsName){
            $dataSourceCode .= ",\n". sprintf('            "dataSource"=>"%s"', $dsName);
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
}', "\\". $dsNameForPath, $tableNameForPath, $tableName, $fieldsCode, $constraintsCode, $autoIncrementCode, $dataSourceCode);
    }
    public function build(string $dataSource): ?array{
        $dsName = ($dataSource) ? $dataSource : "default";
        $dsNameForPath = ($dsName == "default") ? "__". $dsName. "__" : $dsName;
        $cfg = $this->tfphp->getConfig()["database"][$dsName];
        $dbs = $this->tfphp->getDataSource($dsName);
        if(!$dbs){
            return null;
        }
        $tableInfos = $dbs->fetchAll("show tables", []);
        $result = [];
        foreach ($tableInfos as $tableInfo){
            foreach ($tableInfo as $field => $value){
                if(substr($field, 0, 10) == "Tables_in_"){
                    $dbName = substr($field, 10);
                    $tableName = $value;
                    $tableNameForPath = (isset($cfg["table_prefix"]) && $cfg["table_prefix"] && strpos($tableName, $cfg["table_prefix"]) === 0) ? substr($tableName, strlen($cfg["table_prefix"])) : $tableName;
                    $fields = [];
                    $constraints = [];
                    $autoIncrementField = null;
                    $createTableInfos = $dbs->fetchAll("show create table `". $tableName. "`", []);
                    $createTableInfo = $createTableInfos[0];
                    $createTableName = $createTableInfo["Table"];
                    $createTableSQL = $createTableInfo["Create Table"];
                    $createTableSQLLines = explode("\n", $createTableSQL);
                    for($i=0;$i<count($createTableSQLLines);$i++){
                        $createTableSQLLine = $createTableSQLLines[$i];
                        if(preg_match("/^[\s\t]*\`([^\`]+)\`[\s\t]*([a-z0-9]+)/", $createTableSQLLine, $rg)){
                            $fieldName = $rg[1];
                            $fieldType = $rg[2];
                            $autoIncrement = false;
                            if(preg_match("/^(tinyint|smallint|mediumint|int|bigint)/i", $fieldType)){
                                $fieldType = "tfdao::FIELD_TYPE_INT";
                                if(preg_match("/[\s\t]AUTO_INCREMENT[\s\t\r\n,]/", $createTableSQLLine)){
                                    $autoIncrement = true;
                                    $autoIncrementField = $fieldName;
                                }
                            }
                            else{
                                $fieldType = "tfdao::FIELD_TYPE_STR";
                            }
                            $required = false;
                            if(preg_match("/[\s\t]+not[\s\t]+null/i", $createTableSQLLine) && !preg_match("/[\s\t]+default[\s\t]+/i", $createTableSQLLine) && !$autoIncrement){
                                $required = true;
                            }
                            $fields[$fieldName] = ["@type"=>$fieldType];
                            if($required){
                                $fields[$fieldName]["@required"] = "true";
                            }
                        }
                        else if(preg_match("/^[\s\t]*(PRIMARY KEY|UNIQUE KEY|KEY)?([^\(]+)\(([^\)]+)\)/", $createTableSQLLine, $rg)){
                            $constraintType = trim($rg[1]);
                            if(!in_array($constraintType, ["KEY"])){
                                $constraintName = str_replace("`", "", trim($rg[2]));
                                if($constraintName == ""){
                                    $constraintName = "default";
                                }
                                $constraintFields = explode(",", str_replace("`", "", $rg[3]));
                                $constraints[$constraintName] = $constraintFields;
                            }
                        }
                    }
                    $this->makeClassCode($dsName, $dsNameForPath, $dbName, $tableName, $tableNameForPath, $fields, $constraints, $autoIncrementField);
                    $classFilepath = TFPHP_DOCUMENT_ROOT. "/model/dao/". $dsNameForPath. "/". $tableNameForPath. ".inc.php";
                    $classDirpath = dirname($classFilepath);
                    $mkdirRet = true;
                    if(!file_exists($classDirpath)){
                        $mkdirRet = mkdir($classDirpath, 0777, true);
                    }
                    $writeFileRet = file_put_contents($classFilepath, $this->makeClassCode($dsName, $dsNameForPath, $dbName, $tableName, $tableNameForPath, $fields, $constraints, $autoIncrementField));
                    $result[] = [
                        "ds"=>$dsName,
                        "db"=>$dbName,
                        "table"=>$tableName,
                        "result"=>($mkdirRet && $writeFileRet > 0)
                    ];
                }
            }
        }
        return $result;
    }
}