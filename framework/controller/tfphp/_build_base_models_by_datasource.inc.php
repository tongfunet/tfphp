<?php

namespace tfphp\framework\controller\tfphp;

use tfphp\framework\system\tfapi;

class _build_base_models_by_datasource extends tfapi {
    private function makeClassCode(string $dbName, string $tableName, array $fields, array $constraints, ?string $autoIncrementField){
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
        return sprintf('<?php

namespace tfproject\model\dao%s;

use tfphp\tfphp;
use tfphp\model\tfdao;
use tfphp\model\tfdaoSingle;

class %s extends tfdaoSingle{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp, [
            "name"=>"%s",
            "fields"=>[
%s
            ]%s%s
        ]);
    }
}', ($_GET["withDatabaseNamespace"] == "yes") ? "\\". $dbName : "", $tableName, $tableName, $fieldsCode, $constraintsCode, $autoIncrementCode);
    }
    protected function onLoad(){
        $dbs = $this->tfphp->getDataSource();
        $tableInfos = $dbs->fetchAll("show tables", []);
        $result = [];
        foreach ($tableInfos as $tableInfo){
            foreach ($tableInfo as $field => $value){
                if(substr($field, 0, 10) == "Tables_in_"){
                    $dbName = substr($field, 10);
                    $tableName = $value;
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
//                        var_dump($createTableSQLLine);
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
//                            var_dump($tableName. ".". $fieldName, $fieldType, $required);
                            $fields[$fieldName] = ["@type"=>$fieldType];
                            if($required){
                                $fields[$fieldName]["@required"] = "true";
                            }
                        }
                        else if(preg_match("/^[\s\t]*(PRIMARY KEY|UNIQUE KEY|KEY)?([^\(]+)\(([^\)]+)\)/", $createTableSQLLine, $rg)){
                            $constraintType = trim($rg[1]);
                            $constraintName = str_replace("`", "", trim($rg[2]));
                            if($constraintName == ""){
                                $constraintName = "default";
                            }
                            $constraintFields = explode(",", str_replace("`", "", $rg[3]));
//                            var_dump($constraintType, $constraintName, $constraintFields);
                            $constraints[$constraintName] = $constraintFields;
                        }
                    }
                    $this->makeClassCode($dbName, $tableName, $fields, $constraints, $autoIncrementField);
                    $classFilepath = TFPHP_DOCUMENT_ROOT. "/web-inf/model/dao". (($_GET["withDatabaseNamespace"] == "yes") ? "/". $dbName : ""). "/". $tableName. ".inc.php";
                    $mkdirRet = true;
                    if(!file_exists($classFilepath)){
                        $mkdirRet = mkdir(dirname($classFilepath), 0777);
                    }
                    $writeFileRet = file_put_contents($classFilepath, $this->makeClassCode($dbName, $tableName, $fields, $constraints, $autoIncrementField));
                    $result[] = [
                        "db"=>$dbName,
                        "table"=>$tableName,
                        "result"=>($mkdirRet && $writeFileRet > 0)
                    ];
                }
            }
        }
        $this->responseJsonData([
            "errcode"=>0,
            "errmsg"=>"OK",
            "result"=>$result
        ]);
    }
}