<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\model;

use tfphp\framework\tfphp;

/**
 * Class tfdaoOneToMany
 * @package tfphp\framework\model
 * @datetime 2025/7/18
 */
class tfdaoOneToMany extends tfdao {
    private ?string $lastError;
    protected array $tables;
    protected array $relationParams;
    protected ?array $options;
    public function __construct(tfphp $tfphp, array $tables, array $relationParams, array $options=null){
        parent::__construct($tfphp);
        $this->lastError = null;
        $this->tables = $tables;
        $this->relationParams = $relationParams;
        $this->options = $options;
        if(count($this->tables) != 2){
            throw new \Exception("the number of tables must be 2", 660501);
        }
        if(count($this->relationParams) != 1){
            throw new \Exception("the number of relation parameters must be 1", 660502);
        }
        foreach ($this->relationParams as $k => $relationParam){
            if(!isset($this->relationParams[$k]["type"])) $this->relationParams[$k]["type"] = tfdao::RELATION_PARAM_TYPE_ARRAY;
            switch ($this->relationParams[$k]["type"]){
                case tfdao::RELATION_PARAM_TYPE_ARRAY:
                    if(!isset($relationParam["mapping"]) || !is_array($relationParam["mapping"])){
                        throw new \Exception("the relation parameter ". $k. " is of type array and require 'mapping' param", 660503);
                    }
                    break;
                case tfdao::RELATION_PARAM_TYPE_SQL:
                    if(empty($relationParam["sql"])){
                        throw new \Exception("the relation parameter ". $k. " is of type array and require 'sql' param", 660504);
                    }
                    preg_match_all("/a\.([a-zA-Z0-9\_\-]+)[\s\t\r\n]*\=[\s\t\r\n]*b\.([a-zA-Z0-9\_\-]+)/", $relationParam["sql"], $rgs);
                    $this->relationParams[$k]["mapping"] = [];
                    foreach ($rgs[1] as $m => $v){
                        $this->relationParams[$k]["mapping"][$v] = $rgs[2][$m];
                        $this->relationParams[$k]["sql"] = str_replace("a.". $v, "?", $this->relationParams[$k]["sql"]);
                    }
                    break;
                default:
                    throw new \Exception("invalid type of relation parameter", 660505);
            }
        }
    }
}